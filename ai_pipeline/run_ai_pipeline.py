import os
import re
import json
import pickle
import warnings
from datetime import datetime

import numpy as np
import pandas as pd

from catboost import CatBoostRegressor, CatBoostClassifier
from sklearn.ensemble import IsolationForest
from sklearn.model_selection import train_test_split
from sklearn.metrics import (
    mean_absolute_error,
    mean_squared_error,
    r2_score,
    accuracy_score,
    precision_score,
    recall_score,
    f1_score,
)

warnings.filterwarnings("ignore")


BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

INPUT_DIR = os.path.join(BASE_DIR, "storage", "app", "ai", "input")
OUTPUT_DIR = os.path.join(BASE_DIR, "storage", "app", "ai", "output")
MODEL_DIR = os.path.join(BASE_DIR, "storage", "app", "ai", "models")

ITEM_MODEL_PATH = os.path.join(MODEL_DIR, "catboost_barang_forecasting_laravel.cbm")
ROOM_MODEL_PATH = os.path.join(MODEL_DIR, "catboost_slot_occupancy_laravel.cbm")
ANOMALY_MODEL_PATH = os.path.join(MODEL_DIR, "isolation_forest_ruangan_laravel.pkl")
THRESHOLD_PATH = os.path.join(MODEL_DIR, "threshold_slot_occupancy_laravel.json")


# ============================================================
# BASIC UTILITIES
# ============================================================

def ensure_dirs():
    os.makedirs(INPUT_DIR, exist_ok=True)
    os.makedirs(OUTPUT_DIR, exist_ok=True)
    os.makedirs(MODEL_DIR, exist_ok=True)


def read_csv_safe(filename):
    path = os.path.join(INPUT_DIR, filename)

    if not os.path.exists(path):
        print(f"[WARNING] File input tidak ditemukan: {filename}")
        return pd.DataFrame()

    try:
        df = pd.read_csv(path)
        df.columns = df.columns.str.strip()
        return df
    except pd.errors.EmptyDataError:
        print(f"[WARNING] File input kosong: {filename}")
        return pd.DataFrame()


def save_csv(df, filename):
    path = os.path.join(OUTPUT_DIR, filename)
    df.to_csv(path, index=False)
    print(f"[OK] Output dibuat: {filename} ({len(df)} rows)")


def safe_numeric(series, default=0):
    return pd.to_numeric(series, errors="coerce").fillna(default)


def data_status(history_count):
    if history_count <= 0:
        return "belum_ada_data"
    if history_count < 3:
        return "data_terbatas"
    return "cukup_data"


def prediction_status(value):
    value = float(value or 0)

    if value >= 3:
        return "tinggi"
    if value >= 1:
        return "sedang"
    return "rendah"

def semester_decision(row):
    stock = float(row.get("stock", 0) or 0)
    minimum_stock = float(row.get("minimum_stock", 0) or 0)
    history_count = int(row.get("history_count", 0) or 0)

    predicted_next_usage = float(row.get("predicted_next_usage", 0) or 0)
    predicted_semester_usage = float(row.get("predicted_semester_usage", 0) or 0)
    estimated_semester_borrowing_count = float(row.get("estimated_semester_borrowing_count", 0) or 0)

    semester_stock_gap = max(
        0,
        (predicted_semester_usage + minimum_stock) - stock
    )

    if history_count <= 0:
        status = "Belum ada data"
        recommendation = "Belum dapat direkomendasikan"
        reason = "Item belum memiliki histori peminjaman completed sehingga kebutuhan semester belum bisa diperkirakan."

    elif stock <= minimum_stock:
        status = "Kritis"
        recommendation = "Restock segera"
        reason = (
            f"Stok saat ini {stock:.0f} unit sudah berada di bawah atau sama dengan "
            f"minimum stok {minimum_stock:.0f} unit."
        )

    elif semester_stock_gap > 0:
        status = "Perlu restock"
        recommendation = "Restock untuk kebutuhan semester"
        reason = (
            f"Prediksi kebutuhan 6 bulan adalah {predicted_semester_usage:.2f} unit "
            f"dengan estimasi {estimated_semester_borrowing_count:.2f} peminjaman. "
            f"Agar stok tetap di atas minimum stok, dibutuhkan tambahan sekitar "
            f"{semester_stock_gap:.2f} unit."
        )

    elif stock > 0 and predicted_semester_usage >= (stock * 0.7):
        status = "Pantau"
        recommendation = "Pantau stok selama semester berjalan"
        reason = (
            f"Prediksi kebutuhan 6 bulan adalah {predicted_semester_usage:.2f} unit, "
            f"mendekati sebagian besar stok saat ini {stock:.0f} unit."
        )

    else:
        status = "Aman"
        recommendation = "Stok cukup untuk estimasi semester"
        reason = (
            f"Prediksi kebutuhan 6 bulan adalah {predicted_semester_usage:.2f} unit "
            f"dengan stok saat ini {stock:.0f} unit dan minimum stok {minimum_stock:.0f} unit."
        )

    return pd.Series({
        "semester_stock_gap": round(semester_stock_gap, 2),
        "semester_stock_status": status,
        "semester_recommendation": recommendation,
        "semester_reason": reason,
    })

def occupancy_status(value):
    value = float(value or 0)

    if value >= 0.70:
        return "tinggi"
    if value >= 0.35:
        return "sedang"
    return "rendah"


def parse_time_slot(time_slot):
    """
    Format didukung:
    7.30-08.30
    07.30-08.30
    07:30-08:30
    """
    if pd.isna(time_slot):
        return None, None

    text = str(time_slot).strip().replace(":", ".")
    parts = re.split(r"\s*-\s*", text)

    if len(parts) != 2:
        return None, None

    def parse_one(value):
        value = value.strip()

        match = re.match(r"^(\d{1,2})(?:\.(\d{1,2}))?$", value)

        if not match:
            return None

        hour = int(match.group(1))
        minute = int(match.group(2) or 0)

        return hour + (minute / 60)

    start = parse_one(parts[0])
    end = parse_one(parts[1])

    return start, end


def slot_start_int(time_slot):
    start, _ = parse_time_slot(time_slot)

    if start is None:
        return 0

    return int(np.floor(start))


def slot_duration(time_slot):
    start, end = parse_time_slot(time_slot)

    if start is None or end is None or end <= start:
        return 1.0

    return float(end - start)


def default_slots():
    return [
        "07.30-08.30",
        "08.30-09.30",
        "09.30-10.30",
        "10.30-11.30",
        "11.30-12.30",
        "12.30-13.30",
        "13.30-14.30",
        "14.30-15.30",
        "15.30-16.30",
    ]


def to_date(series):
    return pd.to_datetime(series, errors="coerce")


# ============================================================
# LOAD INPUTS
# ============================================================

def load_inputs():
    borrowings = read_csv_safe("borrowings.csv")
    borrowing_items = read_csv_safe("borrowing_items.csv")
    items = read_csv_safe("items.csv")
    rooms = read_csv_safe("rooms.csv")
    room_schedules = read_csv_safe("room_schedules.csv")

    return borrowings, borrowing_items, items, rooms, room_schedules


def prepare_inputs(borrowings, borrowing_items, items, rooms, room_schedules):
    if not borrowings.empty:
        borrowings = borrowings.copy()
        borrowings["borrow_date"] = to_date(borrowings["borrow_date"])
        borrowings = borrowings.dropna(subset=["borrow_date"]).copy()
        borrowings["room_id"] = safe_numeric(borrowings["room_id"]).astype(int)
        borrowings["tanggal"] = borrowings["borrow_date"].dt.date
        borrowings["hari_num"] = borrowings["borrow_date"].dt.dayofweek
        borrowings["bulan"] = borrowings["borrow_date"].dt.month
        borrowings["minggu_ke"] = borrowings["borrow_date"].dt.isocalendar().week.astype(int)
        borrowings["is_weekend"] = (borrowings["hari_num"] >= 5).astype(int)
        borrowings["jam_slot"] = borrowings["time_slot"].apply(slot_start_int)
        borrowings["durasi_jam"] = borrowings["time_slot"].apply(slot_duration)

    if not borrowing_items.empty:
        borrowing_items = borrowing_items.copy()
        borrowing_items["item_id"] = safe_numeric(borrowing_items["item_id"]).astype(int)
        borrowing_items["borrowing_id"] = safe_numeric(borrowing_items["borrowing_id"]).astype(int)
        borrowing_items["qty"] = safe_numeric(borrowing_items["qty"]).astype(int)
        borrowing_items["returned_qty"] = safe_numeric(borrowing_items["returned_qty"]).astype(int)

    if not items.empty:
        items = items.copy()
        items["id"] = safe_numeric(items["id"]).astype(int)
        items["room_id"] = safe_numeric(items["room_id"]).astype(int)
        items["stock"] = safe_numeric(items["stock"]).astype(int)
        items["minimum_stock"] = safe_numeric(items["minimum_stock"]).astype(int)
        items["name"] = items["name"].fillna("-")
        items["type"] = items["type"].fillna("-")

    if not rooms.empty:
        rooms = rooms.copy()
        rooms["id"] = safe_numeric(rooms["id"]).astype(int)
        rooms["name"] = rooms["name"].fillna("-")

    if not room_schedules.empty:
        room_schedules = room_schedules.copy()
        room_schedules["room_id"] = safe_numeric(room_schedules["room_id"]).astype(int)
        room_schedules["time_slot"] = room_schedules["time_slot"].fillna("")
        room_schedules["jam_slot"] = room_schedules["time_slot"].apply(slot_start_int)

    return borrowings, borrowing_items, items, rooms, room_schedules


# ============================================================
# ITEM FORECASTING
# ============================================================

def build_item_usage_daily(borrowings, borrowing_items):
    if borrowings.empty or borrowing_items.empty:
        return pd.DataFrame(columns=[
            "tanggal",
            "item_id",
            "room_id",
            "jumlah_digunakan",
            "hari_num",
            "bulan",
            "minggu_ke",
            "is_weekend",
        ])

    borrowings_small = borrowings[[
        "id",
        "borrow_date",
        "tanggal",
        "room_id",
        "hari_num",
        "bulan",
        "minggu_ke",
        "is_weekend",
    ]].rename(columns={"id": "borrowing_id"})

    df = borrowing_items.merge(
        borrowings_small,
        on="borrowing_id",
        how="inner",
    )

    if df.empty:
        return pd.DataFrame(columns=[
            "tanggal",
            "item_id",
            "room_id",
            "jumlah_digunakan",
            "hari_num",
            "bulan",
            "minggu_ke",
            "is_weekend",
        ])

    df["tanggal"] = pd.to_datetime(df["tanggal"])

    daily = (
        df
        .groupby([
            "tanggal",
            "item_id",
            "room_id",
            "hari_num",
            "bulan",
            "minggu_ke",
            "is_weekend",
        ])["qty"]
        .sum()
        .reset_index(name="jumlah_digunakan")
        .sort_values(["item_id", "room_id", "tanggal"])
    )

    return daily


def prepare_item_features(daily):
    if daily.empty:
        return daily

    daily = daily.copy()
    group_cols = ["item_id", "room_id"]

    daily["lag_1"] = (
        daily
        .groupby(group_cols)["jumlah_digunakan"]
        .shift(1)
        .fillna(0)
    )

    daily["rolling_mean_3"] = (
        daily
        .groupby(group_cols)["jumlah_digunakan"]
        .transform(lambda x: x.shift(1).rolling(3, min_periods=1).mean())
        .fillna(0)
    )

    daily["rolling_mean_7"] = (
        daily
        .groupby(group_cols)["jumlah_digunakan"]
        .transform(lambda x: x.shift(1).rolling(7, min_periods=1).mean())
        .fillna(0)
    )

    daily["history_count_to_date"] = (
        daily
        .groupby(group_cols)
        .cumcount()
    )

    daily["is_awal_semester"] = daily["bulan"].isin([1, 2, 8, 9]).astype(int)
    daily["is_ujian"] = daily["bulan"].isin([3, 6, 10, 12]).astype(int)

    daily["target_next_usage"] = (
        daily
        .groupby(group_cols)["jumlah_digunakan"]
        .shift(-1)
    )

    return daily


def build_item_predictions(borrowings, borrowing_items, items, rooms):
    eval_row = {
        "model": "CatBoost Forecasting Barang",
        "model_type": "barang",
        "MAE": None,
        "RMSE": None,
        "R2": None,
        "Accuracy": None,
        "Precision": None,
        "Recall": None,
        "F1": None,
        "note": None,
    }

    if items.empty:
        empty = pd.DataFrame(columns=[
            "item_id",
            "item_name",
            "item_type",
            "room_id",
            "room_name",
            "stock",
            "minimum_stock",
            "history_count",
            "data_status",
            "last_used_qty",
            "predicted_next_usage",
            "prediction_status",
            
            "predicted_semester_usage",
            "estimated_semester_borrowing_count",
            "semester_stock_gap",
            "semester_stock_status",
            "semester_recommendation",
            "semester_reason",
        ])

        eval_row["note"] = "Tidak ada item di database."

        return empty, pd.DataFrame([eval_row])

    rooms_small = rooms[["id", "name"]].rename(columns={
        "id": "room_id",
        "name": "room_name",
    }) if not rooms.empty else pd.DataFrame(columns=["room_id", "room_name"])

    base = items.merge(rooms_small, on="room_id", how="left")

    daily = build_item_usage_daily(borrowings, borrowing_items)
    daily = prepare_item_features(daily)

    if not borrowing_items.empty:
        history = (
            borrowing_items
            .groupby("item_id")
            .size()
            .reset_index(name="history_count")
        )
    else:
        history = pd.DataFrame(columns=["item_id", "history_count"])

    if not borrowings.empty and not borrowing_items.empty:
        borrowing_dates = borrowings[[
            "id",
            "borrow_date",
        ]].rename(columns={
            "id": "borrowing_id",
        })

        semester_source = borrowing_items.merge(
            borrowing_dates,
            on="borrowing_id",
            how="inner",
        )

        if not semester_source.empty:
            semester_source["year_month"] = (
                semester_source["borrow_date"]
                .dt.to_period("M")
                .astype(str)
            )

            item_semester_stats = (
                semester_source
                .groupby("item_id")
                .agg(
                    active_month_count=("year_month", "nunique"),
                    semester_borrowing_history_count=("borrowing_id", "nunique"),
                    total_usage_history=("qty", "sum"),
                )
                .reset_index()
                .rename(columns={
                    "item_id": "id",
                })
            )
        else:
            item_semester_stats = pd.DataFrame(columns=[
                "id",
                "active_month_count",
                "semester_borrowing_history_count",
                "total_usage_history",
            ])
    else:
        item_semester_stats = pd.DataFrame(columns=[
            "id",
            "active_month_count",
            "semester_borrowing_history_count",
            "total_usage_history",
        ])

    if not daily.empty:
        latest = (
            daily
            .sort_values("tanggal")
            .groupby(["item_id", "room_id"])
            .tail(1)
            .copy()
        )

        latest["fallback_prediction"] = latest[["rolling_mean_3", "lag_1"]].max(axis=1)
    else:
        latest = pd.DataFrame(columns=[
            "item_id",
            "room_id",
            "jumlah_digunakan",
            "fallback_prediction",
        ])

    predicted_latest = latest[[
        "item_id",
        "room_id",
        "jumlah_digunakan",
        "fallback_prediction",
    ]].rename(columns={
        "jumlah_digunakan": "last_used_qty",
    })

    predicted_latest["predicted_next_usage"] = predicted_latest[
        ["fallback_prediction", "last_used_qty"]
    ].max(axis=1).fillna(0)
    
    feature_cols = [
        "item_id",
        "room_id",
        "hari_num",
        "bulan",
        "minggu_ke",
        "is_weekend",
        "is_awal_semester",
        "is_ujian",
        "lag_1",
        "rolling_mean_3",
        "rolling_mean_7",
        "history_count_to_date",
    ]

    cat_features = ["item_id", "room_id"]

    model_data = daily.dropna(subset=["target_next_usage"]).copy() if not daily.empty else pd.DataFrame()

    can_train = (
        len(model_data) >= 10
        and model_data["target_next_usage"].nunique() > 1
        and model_data["item_id"].nunique() >= 1
    )

    if can_train:
        X = model_data[feature_cols].copy()
        y = model_data["target_next_usage"].astype(float)

        X["item_id"] = X["item_id"].astype(str)
        X["room_id"] = X["room_id"].astype(str)

        test_size = 0.2 if len(X) >= 20 else 0.3

        X_train, X_test, y_train, y_test = train_test_split(
            X,
            y,
            test_size=test_size,
            shuffle=False,
        )

        model = CatBoostRegressor(
            iterations=300,
            learning_rate=0.05,
            depth=6,
            loss_function="RMSE",
            random_seed=42,
            verbose=False,
        )

        model.fit(
            X_train,
            y_train,
            cat_features=cat_features,
        )

        pred_test = np.maximum(model.predict(X_test), 0)

        eval_row["MAE"] = round(mean_absolute_error(y_test, pred_test), 6)
        eval_row["RMSE"] = round(np.sqrt(mean_squared_error(y_test, pred_test)), 6)

        try:
            eval_row["R2"] = round(r2_score(y_test, pred_test), 6)
        except Exception:
            eval_row["R2"] = None

        model.save_model(ITEM_MODEL_PATH)

        if not latest.empty:
            X_latest = latest[feature_cols].copy()
            X_latest["item_id"] = X_latest["item_id"].astype(str)
            X_latest["room_id"] = X_latest["room_id"].astype(str)

            latest_pred_values = np.maximum(model.predict(X_latest), 0)

            predicted_latest = latest[[
                "item_id",
                "room_id",
                "jumlah_digunakan",
            ]].rename(columns={
                "jumlah_digunakan": "last_used_qty",
            })

            predicted_latest["predicted_next_usage"] = np.round(latest_pred_values, 2)

        eval_row["note"] = "Model CatBoost barang dilatih dari database Laravel."
    else:
        eval_row["note"] = "Data belum cukup untuk training CatBoost. Menggunakan fallback rolling usage."

    result = base.merge(
        history,
        left_on="id",
        right_on="item_id",
        how="left",
    )

    result = result.merge(
        predicted_latest[[
            "item_id",
            "room_id",
            "last_used_qty",
            "predicted_next_usage",
        ]],
        left_on=["id", "room_id"],
        right_on=["item_id", "room_id"],
        how="left",
        suffixes=("", "_pred"),
    )
    
    result = result.merge(
        item_semester_stats,
        on="id",
        how="left",
    )

    result["history_count"] = result["history_count"].fillna(0).astype(int)
    result["last_used_qty"] = result["last_used_qty"].fillna(0).astype(int)
    result["predicted_next_usage"] = result["predicted_next_usage"].fillna(0).astype(float)

    # Jika item sudah punya histori tetapi prediksi masih 0,
    # gunakan jumlah pemakaian terakhir sebagai fallback data terbatas.
    result.loc[
        (result["history_count"] > 0) & (result["predicted_next_usage"] <= 0),
        "predicted_next_usage"
    ] = result.loc[
        (result["history_count"] > 0) & (result["predicted_next_usage"] <= 0),
        "last_used_qty"
    ]

    result["predicted_next_usage"] = result["predicted_next_usage"].round(2)
    
    result["active_month_count"] = result["active_month_count"].fillna(0).astype(float)
    result["semester_borrowing_history_count"] = result["semester_borrowing_history_count"].fillna(0).astype(float)
    result["total_usage_history"] = result["total_usage_history"].fillna(0).astype(float)

    result["estimated_monthly_borrowing_count"] = np.where(
        result["active_month_count"] > 0,
        result["semester_borrowing_history_count"] / result["active_month_count"],
        0
    )

    result["estimated_semester_borrowing_count"] = (
        result["estimated_monthly_borrowing_count"] * 6
    )

    # Jika sudah ada histori tetapi estimasi semester masih 0,
    # minimal anggap ada 1 peminjaman dalam 6 bulan sebagai fallback aman.
    result.loc[
        (result["history_count"] > 0) & (result["estimated_semester_borrowing_count"] <= 0),
        "estimated_semester_borrowing_count"
    ] = 1

    result["estimated_semester_borrowing_count"] = (
        result["estimated_semester_borrowing_count"]
        .fillna(0)
        .round(2)
    )

    result["predicted_semester_usage"] = (
        result["predicted_next_usage"] * result["estimated_semester_borrowing_count"]
    ).fillna(0).round(2)

    semester_columns = result.apply(semester_decision, axis=1)
    result = pd.concat([result, semester_columns], axis=1)

    output = pd.DataFrame({
        "item_id": result["id"],
        "item_name": result["name"],
        "item_type": result["type"],
        "room_id": result["room_id"],
        "room_name": result["room_name"].fillna("-"),
        "stock": result["stock"].fillna(0).astype(int),
        "minimum_stock": result["minimum_stock"].fillna(0).astype(int),
        "history_count": result["history_count"],
        "data_status": result["history_count"].apply(data_status),
        "last_used_qty": result["last_used_qty"],
        "predicted_next_usage": result["predicted_next_usage"],
        "prediction_status": result["predicted_next_usage"].apply(prediction_status),
        
        "predicted_semester_usage": result["predicted_semester_usage"],
        "estimated_semester_borrowing_count": result["estimated_semester_borrowing_count"],
        "semester_stock_gap": result["semester_stock_gap"],
        "semester_stock_status": result["semester_stock_status"],
        "semester_recommendation": result["semester_recommendation"],
        "semester_reason": result["semester_reason"],
    })

    return output, pd.DataFrame([eval_row])


# ============================================================
# ROOM SLOT FORECASTING
# ============================================================

def build_room_slot_master(rooms, room_schedules):
    columns = ["room_id", "room_name", "time_slot", "jam_slot"]

    if rooms.empty:
        return pd.DataFrame(columns=columns)

    room_name_map = rooms.set_index("id")["name"].to_dict()

    rows = []

    if not room_schedules.empty:
        schedule_slots = (
            room_schedules[["room_id", "time_slot", "jam_slot"]]
            .drop_duplicates()
            .sort_values(["room_id", "jam_slot", "time_slot"])
        )

        for _, row in schedule_slots.iterrows():
            rows.append({
                "room_id": int(row["room_id"]),
                "room_name": room_name_map.get(int(row["room_id"]), "-"),
                "time_slot": row["time_slot"],
                "jam_slot": int(row["jam_slot"]),
            })
    else:
        for _, room in rooms.iterrows():
            for slot in default_slots():
                rows.append({
                    "room_id": int(room["id"]),
                    "room_name": room["name"],
                    "time_slot": slot,
                    "jam_slot": slot_start_int(slot),
                })

    return pd.DataFrame(rows, columns=columns).drop_duplicates()


def build_room_training_slots(borrowings, room_slot_master):
    if borrowings.empty or room_slot_master.empty:
        return pd.DataFrame()

    dates = borrowings["borrow_date"].dropna().dt.date.unique()

    rows = []

    used_lookup = set(
        borrowings
        .assign(date_key=borrowings["borrow_date"].dt.date)
        .apply(lambda r: (r["date_key"], int(r["room_id"]), str(r["time_slot"])), axis=1)
        .tolist()
    )

    for date_value in dates:
        date_ts = pd.to_datetime(date_value)

        for _, slot in room_slot_master.iterrows():
            key = (date_value, int(slot["room_id"]), str(slot["time_slot"]))

            rows.append({
                "date": date_ts,
                "room_id": int(slot["room_id"]),
                "room_name": slot["room_name"],
                "time_slot": slot["time_slot"],
                "jam_slot": int(slot["jam_slot"]),
                "hari_num": date_ts.dayofweek,
                "bulan": date_ts.month,
                "minggu_ke": int(date_ts.isocalendar().week),
                "is_weekend": 1 if date_ts.dayofweek >= 5 else 0,
                "target_used": 1 if key in used_lookup else 0,
            })

    df = pd.DataFrame(rows)

    if df.empty:
        return df

    df = df.sort_values(["room_id", "jam_slot", "date"]).reset_index(drop=True)

    group_cols = ["room_id", "jam_slot"]

    df["lag_1_slot"] = (
        df.groupby(group_cols)["target_used"]
        .shift(1)
        .fillna(0)
    )

    df["rolling_slot_7"] = (
        df.groupby(group_cols)["target_used"]
        .transform(lambda x: x.shift(1).rolling(7, min_periods=1).mean())
        .fillna(0)
    )

    df["rolling_slot_14"] = (
        df.groupby(group_cols)["target_used"]
        .transform(lambda x: x.shift(1).rolling(14, min_periods=1).mean())
        .fillna(0)
    )

    df["room_slot_rate"] = (
        df.groupby(group_cols)["target_used"]
        .transform(lambda x: x.shift(1).expanding().mean())
        .fillna(0)
    )

    df["hour_rate"] = (
        df.groupby("jam_slot")["target_used"]
        .transform(lambda x: x.shift(1).expanding().mean())
        .fillna(0)
    )

    return df

def get_next_working_day():
    target_date = pd.Timestamp.today().normalize() + pd.Timedelta(days=1)

    while target_date.dayofweek >= 5:
        target_date = target_date + pd.Timedelta(days=1)

    return target_date


def build_latest_room_slot_features(room_slot_master, training_slots):
    today = get_next_working_day()

    latest = room_slot_master.copy()
    latest["date"] = today
    latest["hari_num"] = today.dayofweek
    latest["bulan"] = today.month
    latest["minggu_ke"] = int(today.isocalendar().week)
    latest["is_weekend"] = 1 if today.dayofweek >= 5 else 0

    if training_slots.empty:
        latest["lag_1_slot"] = 0
        latest["rolling_slot_7"] = 0
        latest["rolling_slot_14"] = 0
        latest["room_slot_rate"] = 0
        latest["hour_rate"] = 0
        latest["current_status"] = "Tidak Terpakai"
        return latest

    stats = (
        training_slots
        .sort_values("date")
        .groupby(["room_id", "jam_slot"])
        .tail(1)[[
            "room_id",
            "jam_slot",
            "target_used",
            "rolling_slot_7",
            "rolling_slot_14",
            "room_slot_rate",
            "hour_rate",
        ]]
        .rename(columns={
            "target_used": "lag_1_slot",
        })
    )

    latest = latest.merge(
        stats,
        on=["room_id", "jam_slot"],
        how="left",
    )

    fill_cols = [
        "lag_1_slot",
        "rolling_slot_7",
        "rolling_slot_14",
        "room_slot_rate",
        "hour_rate",
    ]

    latest[fill_cols] = latest[fill_cols].fillna(0)
    latest["current_status"] = latest["lag_1_slot"].apply(lambda v: "Terpakai" if v == 1 else "Tidak Terpakai")

    return latest


def build_room_predictions(borrowings, rooms, room_schedules):
    room_eval = {
        "model": "CatBoost Slot Occupancy",
        "model_type": "ruangan",
        "MAE": None,
        "RMSE": None,
        "R2": None,
        "Accuracy": None,
        "Precision": None,
        "Recall": None,
        "F1": None,
        "note": None,
    }

    room_slot_master = build_room_slot_master(rooms, room_schedules)

    if rooms.empty:
        slot_output = pd.DataFrame(columns=[
            "room_id",
            "room_name",
            "jam_slot",
            "time_slot",
            "day",
            "month",
            "history_count",
            "data_status",
            "current_status",
            "probability_used",
            "predicted_status",
            "confidence_score",
        ])

        summary_output = pd.DataFrame(columns=[
            "room_id",
            "room_name",
            "history_count",
            "data_status",
            "total_slot",
            "predicted_used_slot",
            "average_probability_used",
            "predicted_occupancy_rate",
            "predicted_occupancy_status",
            "predicted_peak_hour",
            "peak_hour_probability",
        ])

        room_eval["note"] = "Tidak ada ruangan di database."

        return slot_output, summary_output, pd.DataFrame([room_eval])

    training_slots = build_room_training_slots(borrowings, room_slot_master)
    latest = build_latest_room_slot_features(room_slot_master, training_slots)

    if not borrowings.empty:
        room_history = (
            borrowings
            .groupby("room_id")
            .size()
            .reset_index(name="history_count")
        )
    else:
        room_history = pd.DataFrame(columns=["room_id", "history_count"])

    feature_cols = [
        "room_id",
        "jam_slot",
        "hari_num",
        "bulan",
        "minggu_ke",
        "is_weekend",
        "lag_1_slot",
        "rolling_slot_7",
        "rolling_slot_14",
        "room_slot_rate",
        "hour_rate",
    ]

    cat_features = ["room_id", "jam_slot"]

    can_train = (
        not training_slots.empty
        and len(training_slots) >= 30
        and training_slots["target_used"].nunique() == 2
        and training_slots["target_used"].sum() >= 3
    )

    best_threshold = 0.5

    if can_train:
        X = training_slots[feature_cols].copy()
        y = training_slots["target_used"].astype(int)

        X["room_id"] = X["room_id"].astype(str)
        X["jam_slot"] = X["jam_slot"].astype(str)

        X_train, X_test, y_train, y_test = train_test_split(
            X,
            y,
            test_size=0.2,
            shuffle=False,
        )

        model = CatBoostClassifier(
            iterations=400,
            learning_rate=0.05,
            depth=6,
            loss_function="Logloss",
            auto_class_weights="Balanced",
            random_seed=42,
            verbose=False,
        )

        model.fit(
            X_train,
            y_train,
            cat_features=cat_features,
        )

        prob_test = model.predict_proba(X_test)[:, 1]

        thresholds = np.arange(0.25, 0.81, 0.05)
        best_f1 = -1

        for threshold in thresholds:
            pred = (prob_test >= threshold).astype(int)
            score = f1_score(y_test, pred, zero_division=0)

            if score > best_f1:
                best_f1 = score
                best_threshold = float(threshold)

        pred_test = (prob_test >= best_threshold).astype(int)

        room_eval["Accuracy"] = round(accuracy_score(y_test, pred_test), 6)
        room_eval["Precision"] = round(precision_score(y_test, pred_test, zero_division=0), 6)
        room_eval["Recall"] = round(recall_score(y_test, pred_test, zero_division=0), 6)
        room_eval["F1"] = round(f1_score(y_test, pred_test, zero_division=0), 6)
        room_eval["note"] = f"Model CatBoost slot occupancy dilatih dari database Laravel. Threshold={best_threshold:.2f}"

        model.save_model(ROOM_MODEL_PATH)

        with open(THRESHOLD_PATH, "w", encoding="utf-8") as f:
            json.dump({"threshold": best_threshold}, f, indent=4)

        X_latest = latest[feature_cols].copy()
        X_latest["room_id"] = X_latest["room_id"].astype(str)
        X_latest["jam_slot"] = X_latest["jam_slot"].astype(str)

        latest["probability_used"] = model.predict_proba(X_latest)[:, 1]
    else:
        room_eval["note"] = "Data belum cukup untuk training CatBoost ruangan. Menggunakan fallback historical slot rate."

        latest["probability_used"] = latest[
            ["room_slot_rate", "rolling_slot_7", "rolling_slot_14", "lag_1_slot"]
        ].max(axis=1).fillna(0)

        # Fallback data terbatas:
        # jika slot pernah dipakai minimal sekali, beri probabilitas awal.
        latest.loc[
            (latest["lag_1_slot"] > 0) & (latest["probability_used"] <= 0),
            "probability_used"
        ] = 0.60

        # Jika ruangan sudah punya histori tapi slot tertentu belum pernah dipakai,
        # beri probabilitas rendah, bukan 0 total.
        room_has_history = borrowings["room_id"].unique().tolist() if not borrowings.empty else []

        latest.loc[
            (latest["room_id"].isin(room_has_history)) & (latest["probability_used"] <= 0),
            "probability_used"
        ] = 0.10

    latest["predicted_status"] = latest["probability_used"].apply(
        lambda v: "Terpakai" if float(v) >= best_threshold else "Tidak Terpakai"
    )

    latest["confidence_score"] = latest["probability_used"].apply(
        lambda v: max(float(v), 1 - float(v))
    )

    latest = latest.merge(room_history, on="room_id", how="left")
    latest["history_count"] = latest["history_count"].fillna(0).astype(int)
    latest["data_status"] = latest["history_count"].apply(data_status)

    slot_output = pd.DataFrame({
        "room_id": latest["room_id"],
        "room_name": latest["room_name"],
        "jam_slot": latest["jam_slot"].astype(int),
        "time_slot": latest["time_slot"],
        "day": latest["hari_num"],
        "month": latest["bulan"],
        "history_count": latest["history_count"],
        "data_status": latest["data_status"],
        "current_status": latest["current_status"],
        "probability_used": latest["probability_used"].astype(float).round(3),
        "predicted_status": latest["predicted_status"],
        "confidence_score": latest["confidence_score"].astype(float).round(3),
    })

    summary_rows = []

    for room_id, group in slot_output.groupby("room_id"):
        room_name = group["room_name"].iloc[0]
        history_count = int(group["history_count"].iloc[0])
        total_slot = len(group)
        predicted_used_slot = int((group["predicted_status"] == "Terpakai").sum())
        avg_prob = float(group["probability_used"].mean()) if total_slot > 0 else 0
        occupancy_rate = predicted_used_slot / total_slot if total_slot > 0 else 0

        peak_row = group.sort_values("probability_used", ascending=False).head(1)

        if not peak_row.empty:
            predicted_peak_hour = int(peak_row["jam_slot"].iloc[0])
            peak_hour_probability = float(peak_row["probability_used"].iloc[0])
        else:
            predicted_peak_hour = None
            peak_hour_probability = None

        summary_rows.append({
            "room_id": room_id,
            "room_name": room_name,
            "history_count": history_count,
            "data_status": data_status(history_count),
            "total_slot": total_slot,
            "predicted_used_slot": predicted_used_slot,
            "average_probability_used": round(avg_prob, 3),
            "predicted_occupancy_rate": round(occupancy_rate, 3),
            "predicted_occupancy_status": occupancy_status(occupancy_rate),
            "predicted_peak_hour": predicted_peak_hour,
            "peak_hour_probability": round(peak_hour_probability, 3) if peak_hour_probability is not None else None,
        })

    summary_output = pd.DataFrame(summary_rows)

    return slot_output, summary_output, pd.DataFrame([room_eval])


# ============================================================
# ROOM ANOMALY DETECTION
# ============================================================

def build_room_daily_stats(borrowings, rooms):
    if borrowings.empty:
        return pd.DataFrame(columns=[
            "date",
            "room_id",
            "room_name",
            "total_borrowing",
            "total_duration",
            "occupancy_rate",
        ])

    room_name_map = rooms.set_index("id")["name"].to_dict() if not rooms.empty else {}

    df = borrowings.copy()
    df["date"] = pd.to_datetime(df["borrow_date"]).dt.date
    df["duration"] = df["time_slot"].apply(slot_duration)

    daily = (
        df
        .groupby(["date", "room_id"])
        .agg(
            total_borrowing=("id", "count"),
            total_duration=("duration", "sum"),
        )
        .reset_index()
    )

    max_possible_hours = 9

    daily["occupancy_rate"] = (daily["total_duration"] / max_possible_hours).clip(0, 1)
    daily["room_name"] = daily["room_id"].map(room_name_map).fillna("-")

    return daily


def build_room_anomalies(borrowings, rooms):
    eval_row = {
        "model": "Isolation Forest Anomaly",
        "model_type": "anomali",
        "MAE": None,
        "RMSE": None,
        "R2": None,
        "Accuracy": None,
        "Precision": None,
        "Recall": None,
        "F1": None,
        "note": None,
    }

    daily = build_room_daily_stats(borrowings, rooms)

    if daily.empty or len(daily) < 10:
        eval_row["note"] = "Data belum cukup untuk Isolation Forest. Minimal sekitar 10 data harian."

        output = pd.DataFrame(columns=[
            "room_id",
            "room_name",
            "date",
            "total_borrowing",
            "total_duration",
            "occupancy_rate",
            "anomaly_score",
        ])

        return output, output.copy(), pd.DataFrame([eval_row])

    features = daily[[
        "total_borrowing",
        "total_duration",
        "occupancy_rate",
    ]].copy()

    model = IsolationForest(
        n_estimators=200,
        contamination=0.08,
        random_state=42,
    )

    pred = model.fit_predict(features)
    score = model.decision_function(features)

    daily["is_anomaly"] = (pred == -1).astype(int)
    daily["anomaly_score"] = score

    with open(ANOMALY_MODEL_PATH, "wb") as f:
        pickle.dump(model, f)

    q1 = daily["occupancy_rate"].quantile(0.25)
    q3 = daily["occupancy_rate"].quantile(0.75)
    iqr = q3 - q1
    upper = q3 + 1.5 * iqr

    daily["pseudo_anomaly"] = (daily["occupancy_rate"] > upper).astype(int)

    if daily["pseudo_anomaly"].nunique() > 1:
        eval_row["Accuracy"] = round(accuracy_score(daily["pseudo_anomaly"], daily["is_anomaly"]), 6)
        eval_row["Precision"] = round(precision_score(daily["pseudo_anomaly"], daily["is_anomaly"], zero_division=0), 6)
        eval_row["Recall"] = round(recall_score(daily["pseudo_anomaly"], daily["is_anomaly"], zero_division=0), 6)
        eval_row["F1"] = round(f1_score(daily["pseudo_anomaly"], daily["is_anomaly"], zero_division=0), 6)

    eval_row["note"] = "Isolation Forest dilatih dari statistik harian ruangan."

    anomalies = daily[daily["is_anomaly"] == 1].copy()
    anomalies = anomalies.sort_values("anomaly_score").head(20)

    output_cols = [
        "room_id",
        "room_name",
        "date",
        "total_borrowing",
        "total_duration",
        "occupancy_rate",
        "anomaly_score",
    ]

    all_output = daily[output_cols].copy()
    top_output = anomalies[output_cols].copy()

    all_output["date"] = all_output["date"].astype(str)
    top_output["date"] = top_output["date"].astype(str)

    all_output["total_duration"] = all_output["total_duration"].round(2)
    top_output["total_duration"] = top_output["total_duration"].round(2)

    all_output["occupancy_rate"] = all_output["occupancy_rate"].round(3)
    top_output["occupancy_rate"] = top_output["occupancy_rate"].round(3)

    all_output["anomaly_score"] = all_output["anomaly_score"].round(6)
    top_output["anomaly_score"] = top_output["anomaly_score"].round(6)

    return all_output, top_output, pd.DataFrame([eval_row])


# ============================================================
# METADATA
# ============================================================

def save_metadata():
    metadata = {
        "pipeline_status": "success",
        "pipeline_version": "laravel_ai_pipeline_v1",
        "generated_at": datetime.now().isoformat(),
        "description": "Pipeline AI Laravel menggunakan item_id dan room_id sebagai identitas utama. CatBoost dijalankan saat data cukup, fallback digunakan saat data terbatas.",
    }

    path = os.path.join(OUTPUT_DIR, "metadata_model.json")

    with open(path, "w", encoding="utf-8") as f:
        json.dump(metadata, f, indent=4, ensure_ascii=False)

    print("[OK] Output dibuat: metadata_model.json")


# ============================================================
# MAIN
# ============================================================

def main():
    ensure_dirs()

    print("=== AI Pipeline Laravel Started ===")
    print(f"Input dir : {INPUT_DIR}")
    print(f"Output dir: {OUTPUT_DIR}")
    print(f"Model dir : {MODEL_DIR}")

    borrowings, borrowing_items, items, rooms, room_schedules = load_inputs()

    borrowings, borrowing_items, items, rooms, room_schedules = prepare_inputs(
        borrowings,
        borrowing_items,
        items,
        rooms,
        room_schedules,
    )

    print(f"borrowings      : {len(borrowings)} rows")
    print(f"borrowing_items : {len(borrowing_items)} rows")
    print(f"items           : {len(items)} rows")
    print(f"rooms           : {len(rooms)} rows")
    print(f"room_schedules  : {len(room_schedules)} rows")

    item_predictions, item_eval = build_item_predictions(
        borrowings,
        borrowing_items,
        items,
        rooms,
    )

    slot_predictions, room_summaries, room_eval = build_room_predictions(
        borrowings,
        rooms,
        room_schedules,
    )

    room_anomalies_all, room_anomalies_top, anomaly_eval = build_room_anomalies(
        borrowings,
        rooms,
    )

    save_csv(item_predictions, "prediksi_barang_dashboard.csv")
    save_csv(slot_predictions, "prediksi_slot_ruangan_dashboard.csv")
    save_csv(room_summaries, "ringkasan_prediksi_ruangan_dashboard.csv")
    save_csv(room_anomalies_all, "anomali_ruangan_dashboard.csv")
    save_csv(room_anomalies_top, "top_anomali_ruangan_dashboard.csv")

    save_csv(item_eval, "evaluasi_model_barang.csv")
    save_csv(room_eval, "evaluasi_model_slot_ruangan.csv")
    save_csv(anomaly_eval, "evaluasi_model_anomali.csv")

    save_metadata()

    print("=== AI Pipeline Laravel Finished ===")


if __name__ == "__main__":
    main()