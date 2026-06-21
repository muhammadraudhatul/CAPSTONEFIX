import os
import json
import pandas as pd


BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

INPUT_DIR = os.path.join(BASE_DIR, "storage", "app", "ai", "input")
OUTPUT_DIR = os.path.join(BASE_DIR, "storage", "app", "ai", "output")
MODEL_DIR = os.path.join(BASE_DIR, "storage", "app", "ai", "models")


def ensure_directories():
    os.makedirs(INPUT_DIR, exist_ok=True)
    os.makedirs(OUTPUT_DIR, exist_ok=True)
    os.makedirs(MODEL_DIR, exist_ok=True)


def read_csv_safe(filename):
    path = os.path.join(INPUT_DIR, filename)

    if not os.path.exists(path):
        print(f"[WARNING] File tidak ditemukan: {path}")
        return pd.DataFrame()

    try:
        df = pd.read_csv(path)
        df.columns = df.columns.str.strip()
        return df
    except pd.errors.EmptyDataError:
        print(f"[WARNING] File kosong: {path}")
        return pd.DataFrame()


def save_csv(df, filename):
    path = os.path.join(OUTPUT_DIR, filename)
    df.to_csv(path, index=False)
    print(f"[OK] Output dibuat: {filename} ({len(df)} rows)")


def get_data_status(history_count):
    if history_count <= 0:
        return "belum_ada_data"
    elif history_count < 3:
        return "data_terbatas"
    else:
        return "cukup_data"


def build_item_prediction_placeholder(items, rooms, borrowing_items):
    """
    Versi awal:
    - belum memakai CatBoost
    - hanya memastikan semua item bisa muncul di output
    - kalau belum ada histori, status = belum_ada_data
    """

    if items.empty:
        return pd.DataFrame(columns=[
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
        ])

    items = items.copy()

    rooms_small = rooms[["id", "name"]].rename(columns={
        "id": "room_id",
        "name": "room_name"
    }) if not rooms.empty else pd.DataFrame(columns=["room_id", "room_name"])

    result = items.merge(rooms_small, on="room_id", how="left")

    if not borrowing_items.empty:
        history = (
            borrowing_items
            .groupby("item_id")
            .size()
            .reset_index(name="history_count")
        )

        last_used = (
            borrowing_items
            .groupby("item_id")["qty"]
            .sum()
            .reset_index(name="last_used_qty")
        )
    else:
        history = pd.DataFrame(columns=["item_id", "history_count"])
        last_used = pd.DataFrame(columns=["item_id", "last_used_qty"])

    result = result.merge(history, left_on="id", right_on="item_id", how="left")
    result = result.merge(last_used, left_on="id", right_on="item_id", how="left")

    result["history_count"] = result["history_count"].fillna(0).astype(int)
    result["last_used_qty"] = result["last_used_qty"].fillna(0).astype(int)

    result["data_status"] = result["history_count"].apply(get_data_status)

    result["predicted_next_usage"] = 0.0
    result["prediction_status"] = "rendah"

    output = pd.DataFrame({
        "item_id": result["id"],
        "item_name": result["name"],
        "item_type": result["type"],
        "room_id": result["room_id"],
        "room_name": result["room_name"],
        "stock": result["stock"].fillna(0).astype(int),
        "minimum_stock": result["minimum_stock"].fillna(0).astype(int),
        "history_count": result["history_count"],
        "data_status": result["data_status"],
        "last_used_qty": result["last_used_qty"],
        "predicted_next_usage": result["predicted_next_usage"],
        "prediction_status": result["prediction_status"],
    })

    return output


def build_room_summary_placeholder(rooms, borrowings):
    """
    Versi awal:
    - belum memakai CatBoost slot occupancy
    - memastikan semua room bisa muncul di output
    """

    if rooms.empty:
        return pd.DataFrame(columns=[
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

    rooms = rooms.copy()

    if not borrowings.empty:
        history = (
            borrowings
            .groupby("room_id")
            .size()
            .reset_index(name="history_count")
        )
    else:
        history = pd.DataFrame(columns=["room_id", "history_count"])

    result = rooms.merge(history, left_on="id", right_on="room_id", how="left")

    result["history_count"] = result["history_count"].fillna(0).astype(int)
    result["data_status"] = result["history_count"].apply(get_data_status)

    output = pd.DataFrame({
        "room_id": result["id"],
        "room_name": result["name"],
        "history_count": result["history_count"],
        "data_status": result["data_status"],
        "total_slot": 0,
        "predicted_used_slot": 0,
        "average_probability_used": 0.0,
        "predicted_occupancy_rate": 0.0,
        "predicted_occupancy_status": "rendah",
        "predicted_peak_hour": None,
        "peak_hour_probability": None,
    })

    return output


def build_room_slot_prediction_placeholder(rooms):
    """
    Versi awal:
    membuat slot default 07.30-16.30 untuk setiap room.
    Nanti akan diganti dengan prediksi CatBoost.
    """

    columns = [
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
    ]

    if rooms.empty:
        return pd.DataFrame(columns=columns)

    slots = [
        (7, "07.30-08.30"),
        (8, "08.30-09.30"),
        (9, "09.30-10.30"),
        (10, "10.30-11.30"),
        (11, "11.30-12.30"),
        (12, "12.30-13.30"),
        (13, "13.30-14.30"),
        (14, "14.30-15.30"),
        (15, "15.30-16.30"),
    ]

    rows = []

    for _, room in rooms.iterrows():
        for jam, time_slot in slots:
            rows.append({
                "room_id": room["id"],
                "room_name": room["name"],
                "jam_slot": jam,
                "time_slot": time_slot,
                "day": None,
                "month": None,
                "history_count": 0,
                "data_status": "belum_ada_data",
                "current_status": "Tidak Terpakai",
                "probability_used": 0.0,
                "predicted_status": "Tidak Terpakai",
                "confidence_score": 0.0,
            })

    return pd.DataFrame(rows, columns=columns)


def build_room_anomaly_placeholder():
    return pd.DataFrame(columns=[
        "room_id",
        "room_name",
        "date",
        "total_borrowing",
        "total_duration",
        "occupancy_rate",
        "anomaly_score",
    ])


def build_model_evaluations_placeholder():
    return pd.DataFrame([
        {
            "model": "CatBoost Forecasting Barang",
            "model_type": "barang",
            "MAE": None,
            "RMSE": None,
            "R2": None,
            "Accuracy": None,
            "Precision": None,
            "Recall": None,
            "F1": None,
            "note": "Pipeline awal berhasil. Model belum dijalankan."
        },
        {
            "model": "CatBoost Slot Occupancy",
            "model_type": "ruangan",
            "MAE": None,
            "RMSE": None,
            "R2": None,
            "Accuracy": None,
            "Precision": None,
            "Recall": None,
            "F1": None,
            "note": "Pipeline awal berhasil. Model belum dijalankan."
        },
        {
            "model": "Isolation Forest Anomaly",
            "model_type": "anomali",
            "MAE": None,
            "RMSE": None,
            "R2": None,
            "Accuracy": None,
            "Precision": None,
            "Recall": None,
            "F1": None,
            "note": "Pipeline awal berhasil. Model belum dijalankan."
        },
    ])


def save_metadata():
    metadata = {
        "pipeline_status": "success",
        "pipeline_version": "initial_laravel_pipeline",
        "description": "Pipeline awal Laravel berhasil membaca input dan membuat output placeholder.",
    }

    path = os.path.join(OUTPUT_DIR, "metadata_model.json")

    with open(path, "w", encoding="utf-8") as f:
        json.dump(metadata, f, indent=4, ensure_ascii=False)

    print("[OK] Output dibuat: metadata_model.json")


def main():
    ensure_directories()

    print("=== AI Pipeline Laravel Started ===")
    print(f"Input dir : {INPUT_DIR}")
    print(f"Output dir: {OUTPUT_DIR}")
    print(f"Model dir : {MODEL_DIR}")

    borrowings = read_csv_safe("borrowings.csv")
    borrowing_items = read_csv_safe("borrowing_items.csv")
    items = read_csv_safe("items.csv")
    rooms = read_csv_safe("rooms.csv")
    room_schedules = read_csv_safe("room_schedules.csv")

    print(f"borrowings      : {len(borrowings)} rows")
    print(f"borrowing_items : {len(borrowing_items)} rows")
    print(f"items           : {len(items)} rows")
    print(f"rooms           : {len(rooms)} rows")
    print(f"room_schedules  : {len(room_schedules)} rows")

    item_predictions = build_item_prediction_placeholder(items, rooms, borrowing_items)
    room_summaries = build_room_summary_placeholder(rooms, borrowings)
    room_slot_predictions = build_room_slot_prediction_placeholder(rooms)
    room_anomalies = build_room_anomaly_placeholder()
    model_evaluations = build_model_evaluations_placeholder()

    save_csv(item_predictions, "prediksi_barang_dashboard.csv")
    save_csv(room_slot_predictions, "prediksi_slot_ruangan_dashboard.csv")
    save_csv(room_summaries, "ringkasan_prediksi_ruangan_dashboard.csv")
    save_csv(room_anomalies, "top_anomali_ruangan_dashboard.csv")
    save_csv(room_anomalies, "anomali_ruangan_dashboard.csv")

    save_csv(
        model_evaluations[model_evaluations["model_type"] == "barang"],
        "evaluasi_model_barang.csv"
    )

    save_csv(
        model_evaluations[model_evaluations["model_type"] == "ruangan"],
        "evaluasi_model_slot_ruangan.csv"
    )

    save_csv(
        model_evaluations[model_evaluations["model_type"] == "anomali"],
        "evaluasi_model_anomali.csv"
    )

    save_metadata()

    print("=== AI Pipeline Laravel Finished ===")


if __name__ == "__main__":
    main()