# ============================================
# FILE: app.py (Flask API untuk Flutter + Laravel)
# ============================================

from flask import Flask, request, jsonify
from flask_cors import CORS
import xgboost as xgb
import pandas as pd
import numpy as np
import pickle
import json
import os
from datetime import datetime, timedelta
import warnings
warnings.filterwarnings('ignore')

app = Flask(__name__)
CORS(app)  # Izinkan akses dari Flutter & Laravel

# ============================================
# KONFIGURASI
# ============================================
BASE_DIR = os.path.dirname(os.path.abspath(__file__))

# ============================================
# LOAD METADATA & INDEX MODEL
# ============================================
def load_json_file(filename):
    """Helper untuk load file JSON"""
    filepath = os.path.join(BASE_DIR, filename)
    if os.path.exists(filepath):
        with open(filepath, 'r') as f:
            return json.load(f)
    return None

METADATA = load_json_file('metadata.json') or {}
MODEL_INDEX = load_json_file('model_index.json') or {'alat': {}, 'bahan': {}}

# ============================================
# LOAD DATA STATIS (CSV)
# ============================================
def load_csv_file(filename):
    """Helper untuk load file CSV"""
    filepath = os.path.join(BASE_DIR, 'data', filename)
    if os.path.exists(filepath):
        return pd.read_csv(filepath)
    return pd.DataFrame()

master_barang = load_csv_file('master_barang.csv')
rata_rata_penggunaan = load_csv_file('rata_rata_penggunaan.csv')
kepadatan_df = load_csv_file('data_kepadatan_ruangan.csv')
trend_bulanan = load_csv_file('data_trend_bulanan.csv')
pola_harian = load_csv_file('data_pola_harian.csv')
jam_tersibuk = load_csv_file('data_jam_tersibuk.csv')
hari_tersibuk = load_csv_file('data_hari_tersibuk.csv')
durasi_rata_rata = load_csv_file('data_durasi_rata_rata.csv')

print(f"Metadata: {METADATA.get('training_date', 'Unknown')}")
print(f"Total items: {len(master_barang)}")
print(f"Total ruangan: {kepadatan_df['Laboratorium'].nunique() if not kepadatan_df.empty else 0}")

# ============================================
# CACHE UNTUK MODEL & SCALER
# ============================================
loaded_models = {}
loaded_scalers = {}

def get_model_and_scaler(nama_barang, tipe):
    """
    Load model dan scaler untuk barang tertentu
    Returns: (model, scaler) or (None, None)
    """
    cache_key = f"{tipe}_{nama_barang}"
    
    # Cek cache
    if cache_key in loaded_models:
        return loaded_models[cache_key], loaded_scalers.get(cache_key)
    
    try:
        # Cek apakah barang ada di index
        if tipe == 'alat':
            model_info = MODEL_INDEX.get('alat', {}).get(nama_barang)
        else:
            model_info = MODEL_INDEX.get('bahan', {}).get(nama_barang)
        
        if not model_info:
            return None, None
        
        # Load model XGBoost
        model_path = os.path.join(BASE_DIR, model_info['file'])
        if os.path.exists(model_path):
            model = xgb.XGBRegressor()
            model.load_model(model_path)
            loaded_models[cache_key] = model
        else:
            return None, None
        
        # Load scaler
        scaler_path = os.path.join(BASE_DIR, model_info.get('scaler_file', ''))
        if os.path.exists(scaler_path):
            with open(scaler_path, 'rb') as f:
                scaler = pickle.load(f)
            loaded_scalers[cache_key] = scaler
        else:
            loaded_scalers[cache_key] = None
        
        return model, loaded_scalers.get(cache_key)
        
    except Exception as e:
        print(f"Error loading model for {nama_barang}: {e}")
        return None, None

def create_features(tanggal, lag_1=0, lag_7=0, rolling_3=0, rolling_7=0):
    """
    Membuat feature vector untuk prediksi
    
    Fitur yang digunakan:
    0: hari (0-6, Senin=0)
    1: bulan (1-12)
    2: minggu_ke (1-53)
    3: is_weekend (0/1)
    4: is_awal_semester (0/1) - bulan Jan, Feb, Aug, Sep
    5: is_ujian (0/1) - bulan Mar, Jun, Oct, Dec
    6: lag_1
    7: lag_7
    8: rolling_mean_3
    9: rolling_mean_7
    """
    return [[
        tanggal.weekday(),                          # hari (0=Senin)
        tanggal.month,                              # bulan
        tanggal.isocalendar().week,                 # minggu_ke
        1 if tanggal.weekday() >= 5 else 0,         # is_weekend
        1 if tanggal.month in [1, 2, 8, 9] else 0,  # is_awal_semester
        1 if tanggal.month in [3, 6, 10, 12] else 0, # is_ujian
        lag_1, lag_7, rolling_3, rolling_7
    ]]

def get_historical_data(nama_barang, days=30):
    """
    Mendapatkan data historis penggunaan barang
    Returns: list of tuples (tanggal, jumlah)
    """
    if master_barang.empty:
        return []
    
    # Filter data untuk barang tersebut
    item_data = master_barang[master_barang['nama_barang'] == nama_barang]
    if item_data.empty:
        return []
    
    # Generate tanggal untuk n hari terakhir
    result = []
    end_date = datetime.now().date()
    
    for i in range(days):
        tanggal = end_date - timedelta(days=i+1)
        # Kita tidak punya data historis per hari yang detail di master_barang
        # Untuk demo, return data dummy
        result.append((tanggal, 0))
    
    return result

# ============================================
# HEALTH CHECK
# ============================================
@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'metadata': METADATA,
        'models_loaded': len(loaded_models),
        'total_items': len(master_barang)
    })

# ============================================
# ENDPOINT 1: PREDIKSI STOK ALAT/BAHAN
# ============================================
@app.route('/predict/stok', methods=['POST'])
def predict_stok():
    """
    Prediksi penggunaan stok untuk alat/bahan tertentu
    
    Request body (JSON):
    {
        "nama_barang": "Tensimeter",
        "tipe": "alat",
        "tanggal": "2025-06-25",
        "stok_saat_ini": 10
    }
    
    Response:
    {
        "success": true,
        "nama_barang": "Tensimeter",
        "tipe": "alat",
        "rata_rata_per_hari": 2.5,
        "prediksi_hari_ini": 3,
        "stok_saat_ini": 10,
        "sisa_hari": 3,
        "perkiraan_tanggal_habis": "2025-06-28",
        "status_stok": "Kritis"
    }
    """
    try:
        data = request.json
        if not data:
            return jsonify({'error': 'No data provided'}), 400
        
        nama_barang = data.get('nama_barang')
        tipe = data.get('tipe', 'alat')
        tanggal_str = data.get('tanggal')
        stok_saat_ini = data.get('stok_saat_ini', 0)
        
        if not nama_barang:
            return jsonify({'error': 'nama_barang is required'}), 400
        
        # Parse tanggal
        if tanggal_str:
            tanggal = datetime.strptime(tanggal_str, '%Y-%m-%d')
        else:
            tanggal = datetime.now()
        
        # Cek apakah barang ada di master
        item_info = master_barang[master_barang['nama_barang'] == nama_barang]
        
        if item_info.empty:
            # Barang tidak ditemukan, coba cari dengan case-insensitive
            item_info = master_barang[master_barang['nama_barang'].str.lower() == nama_barang.lower()]
        
        if item_info.empty:
            available = master_barang['nama_barang'].head(20).tolist()
            return jsonify({
                'error': f'Barang "{nama_barang}" tidak ditemukan',
                'available_items': available
            }), 404
        
        # Ambil rata-rata penggunaan
        rata_rata = float(item_info['rata_rata_per_hari'].values[0])
        
        # Inisialisasi prediksi
        prediksi = round(rata_rata)
        
        # Coba gunakan model jika ada
        model, scaler = get_model_and_scaler(nama_barang, tipe)
        
        if model and scaler:
            try:
                features = create_features(tanggal)
                features_scaled = scaler.transform(features)
                prediksi_model = model.predict(features_scaled)[0]
                prediksi = max(0, round(prediksi_model))
            except Exception as e:
                print(f"Model prediction error for {nama_barang}: {e}")
                # Fallback ke rata-rata
        
        # Hitung perkiraan tanggal habis
        if prediksi > 0 and stok_saat_ini > 0:
            sisa_hari = int(stok_saat_ini / prediksi)
            tanggal_habis = (tanggal + timedelta(days=sisa_hari)).strftime('%Y-%m-%d')
        elif rata_rata > 0 and stok_saat_ini > 0:
            sisa_hari = int(stok_saat_ini / rata_rata)
            tanggal_habis = (tanggal + timedelta(days=sisa_hari)).strftime('%Y-%m-%d')
        else:
            sisa_hari = 999
            tanggal_habis = '-'
        
        # Tentukan status stok
        if sisa_hari <= 14:
            status_stok = 'Kritis'
        elif sisa_hari <= 30:
            status_stok = 'Peringatan'
        else:
            status_stok = 'Aman'
        
        return jsonify({
            'success': True,
            'nama_barang': nama_barang,
            'tipe': tipe,
            'rata_rata_per_hari': round(rata_rata, 2),
            'prediksi_hari_ini': prediksi,
            'stok_saat_ini': stok_saat_ini,
            'sisa_hari': sisa_hari,
            'perkiraan_tanggal_habis': tanggal_habis,
            'status_stok': status_stok
        })
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# ============================================
# ENDPOINT 2: RINGKASAN ANALYTICS ALAT & BAHAN
# ============================================
@app.route('/analytics/alat-bahan/summary', methods=['GET'])
def get_alat_bahan_summary():
    """
    Mendapatkan ringkasan analytics untuk semua alat & bahan
    
    Query params:
    - status: all/kritis/peringatan/aman (default: all)
    - limit: jumlah maksimal item (default: 100)
    """
    try:
        status_filter = request.args.get('status', 'all')
        limit = int(request.args.get('limit', 100))
        
        if master_barang.empty:
            return jsonify({'success': True, 'total': 0, 'items': []})
        
        result = []
        for _, row in master_barang.iterrows():
            rata_rata = row['rata_rata_per_hari']
            nama_barang = row['nama_barang']
            
            # Hitung sisa hari (dummy, karena tidak ada stok real)
            # Untuk summary, kita asumsikan stok = 100
            dummy_stok = 100
            if rata_rata > 0:
                sisa_hari = int(dummy_stok / rata_rata)
            else:
                sisa_hari = 999
            
            if sisa_hari <= 14:
                status = 'Kritis'
            elif sisa_hari <= 30:
                status = 'Peringatan'
            else:
                status = 'Aman'
            
            # Filter berdasarkan status
            if status_filter != 'all' and status != status_filter:
                continue
            
            result.append({
                'id_barang': row.get('id_barang', f"ID_{hash(nama_barang)}"),
                'nama_barang': nama_barang,
                'tipe': row.get('tipe', 'alat'),
                'rata_rata_per_hari': round(rata_rata, 2),
                'frekuensi_total': int(row.get('frekuensi_total', 0)),
                'tanggal_terakhir_digunakan': row.get('tanggal_terakhir_digunakan', '-'),
                'status_stok': status
            })
        
        # Batasi jumlah
        result = result[:limit]
        
        # Hitung statistik
        total_kritis = len([r for r in result if r['status_stok'] == 'Kritis'])
        total_peringatan = len([r for r in result if r['status_stok'] == 'Peringatan'])
        total_aman = len([r for r in result if r['status_stok'] == 'Aman'])
        
        return jsonify({
            'success': True,
            'total': len(result),
            'statistik': {
                'kritis': total_kritis,
                'peringatan': total_peringatan,
                'aman': total_aman
            },
            'items': result
        })
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# ============================================
# ENDPOINT 3: PREDIKSI KEBUTUHAN STOK UNTUK PERIODE
# ============================================
@app.route('/predict/kebutuhan-stok', methods=['POST'])
def predict_kebutuhan_stok():
    """
    Prediksi kebutuhan stok untuk periode tertentu
    
    Request body:
    {
        "nama_barang": "Tensimeter",
        "tipe": "alat",
        "periode_hari": 30,
        "stok_saat_ini": 10
    }
    
    Response:
    {
        "success": true,
        "nama_barang": "Tensimeter",
        "estimasi_kebutuhan": 75,
        "stok_saat_ini": 10,
        "rekomendasi_pembelian": 65,
        "rekomendasi_pesan": "Segera pesan 65 unit"
    }
    """
    try:
        data = request.json
        if not data:
            return jsonify({'error': 'No data provided'}), 400
        
        nama_barang = data.get('nama_barang')
        tipe = data.get('tipe', 'alat')
        periode_hari = data.get('periode_hari', 30)
        stok_saat_ini = data.get('stok_saat_ini', 0)
        
        if not nama_barang:
            return jsonify({'error': 'nama_barang is required'}), 400
        
        # Cek barang di master
        item_info = master_barang[master_barang['nama_barang'] == nama_barang]
        if item_info.empty:
            item_info = master_barang[master_barang['nama_barang'].str.lower() == nama_barang.lower()]
        
        if item_info.empty:
            return jsonify({'error': f'Barang "{nama_barang}" tidak ditemukan'}), 404
        
        rata_rata = float(item_info['rata_rata_per_hari'].values[0])
        
        # Estimasi kebutuhan selama periode
        estimasi_kebutuhan = int(rata_rata * periode_hari)
        
        # Rekomendasi pembelian
        rekomendasi_pembelian = max(0, estimasi_kebutuhan - stok_saat_ini)
        
        # Buat rekomendasi teks
        if rekomendasi_pembelian <= 0:
            rekomendasi_pesan = f"Stok mencukupi untuk {periode_hari} hari ke depan"
        elif rekomendasi_pembelian <= 10:
            rekomendasi_pesan = f"Tambah stok {rekomendasi_pembelian} unit (sedikit)"
        elif rekomendasi_pembelian <= 50:
            rekomendasi_pesan = f"Tambah stok {rekomendasi_pembelian} unit (menengah)"
        else:
            rekomendasi_pesan = f"SEGERA pesan {rekomendasi_pembelian} unit (besar)"
        
        return jsonify({
            'success': True,
            'nama_barang': nama_barang,
            'tipe': tipe,
            'periode_hari': periode_hari,
            'rata_rata_per_hari': round(rata_rata, 2),
            'estimasi_kebutuhan': estimasi_kebutuhan,
            'stok_saat_ini': stok_saat_ini,
            'rekomendasi_pembelian': rekomendasi_pembelian,
            'rekomendasi_pesan': rekomendasi_pesan
        })
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# ============================================
# ENDPOINT 4: RINGKASAN ANALYTICS RUANGAN
# ============================================
@app.route('/analytics/ruangan/summary', methods=['GET'])
def get_ruangan_summary():
    """
    Mendapatkan ringkasan analytics untuk semua ruangan
    
    Response:
    {
        "success": true,
        "total": 5,
        "ruangan_tersibuk": "Laboratorium KMB/DKKD",
        "rooms": [...]
    }
    """
    try:
        if kepadatan_df.empty:
            return jsonify({'success': True, 'total': 0, 'ruangan_tersibuk': None, 'rooms': []})
        
        result = []
        
        # Daftar semua ruangan unik
        all_rooms = kepadatan_df['Laboratorium'].unique()
        
        for nama_ruangan in all_rooms:
            # Hitung frekuensi total
            frekuensi = int(kepadatan_df[kepadatan_df['Laboratorium'] == nama_ruangan]['Frekuensi'].sum())
            
            # Cari jam tersibuk
            jam_data = jam_tersibuk[jam_tersibuk['Laboratorium'] == nama_ruangan] if not jam_tersibuk.empty else pd.DataFrame()
            if not jam_data.empty:
                jam_tersibuk_val = int(jam_data['Jam_Tersibuk'].values[0])
                jam_tersibuk_str = f"{jam_tersibuk_val}:00"
            else:
                jam_tersibuk_str = '-'
            
            # Cari hari tersibuk
            hari_data = hari_tersibuk[hari_tersibuk['Laboratorium'] == nama_ruangan] if not hari_tersibuk.empty else pd.DataFrame()
            if not hari_data.empty:
                hari_tersibuk_val = hari_data['Nama_Hari'].values[0]
            else:
                hari_tersibuk_val = '-'
            
            # Cari rata-rata durasi
            durasi_data = durasi_rata_rata[durasi_rata_rata['Laboratorium'] == nama_ruangan] if not durasi_rata_rata.empty else pd.DataFrame()
            if not durasi_data.empty:
                rata_durasi = float(durasi_data['Durasi_Jam'].values[0])
            else:
                rata_durasi = 0
            
            result.append({
                'nama_ruangan': nama_ruangan,
                'frekuensi_penggunaan': frekuensi,
                'jam_tersibuk': jam_tersibuk_str,
                'hari_tersibuk': hari_tersibuk_val,
                'rata_rata_durasi': round(rata_durasi, 2)
            })
        
        # Urutkan berdasarkan frekuensi (tersibuk pertama)
        result.sort(key=lambda x: x['frekuensi_penggunaan'], reverse=True)
        
        return jsonify({
            'success': True,
            'total': len(result),
            'ruangan_tersibuk': result[0]['nama_ruangan'] if result else None,
            'rooms': result
        })
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# ============================================
# ENDPOINT 5: TREND PENGGUNAAN RUANGAN (BULANAN)
# ============================================
@app.route('/analytics/ruangan/trend', methods=['GET'])
def get_ruangan_trend():
    """
    Mendapatkan trend penggunaan ruangan per bulan
    
    Query params:
    - ruangan: nama ruangan (opsional, jika tidak diberikan akan diagregasi semua)
    """
    try:
        ruangan = request.args.get('ruangan')
        
        if trend_bulanan.empty:
            return jsonify({'success': True, 'labels': [], 'data': []})
        
        if ruangan:
            data = trend_bulanan[trend_bulanan['Laboratorium'] == ruangan]
        else:
            # Agregasi semua ruangan
            data = trend_bulanan.groupby('Bulan_Str')['Jumlah_Peminjaman'].sum().reset_index()
        
        if data.empty:
            return jsonify({'success': True, 'labels': [], 'data': []})
        
        # Pastikan kolom yang benar
        if 'Bulan_Str' in data.columns:
            labels = data['Bulan_Str'].tolist()
            values = data['Jumlah_Peminjaman'].tolist()
        elif 'Bulan' in data.columns:
            labels = data['Bulan'].astype(str).tolist()
            values = data['Jumlah_Peminjaman'].tolist()
        else:
            labels = []
            values = []
        
        return jsonify({
            'success': True,
            'ruangan': ruangan if ruangan else 'Semua Ruangan',
            'labels': labels,
            'data': values
        })
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# ============================================
# ENDPOINT 6: HEATMAP PENGGUNAAN RUANGAN
# ============================================
@app.route('/analytics/ruangan/heatmap', methods=['GET'])
def get_ruangan_heatmap():
    """
    Mendapatkan data untuk heatmap penggunaan ruangan (Jam vs Frekuensi)
    
    Query params:
    - ruangan: nama ruangan (wajib)
    """
    try:
        ruangan = request.args.get('ruangan')
        
        if not ruangan:
            return jsonify({'error': 'ruangan parameter is required'}), 400
        
        if pola_harian.empty:
            return jsonify({'success': True, 'nama_ruangan': ruangan, 'heatmap_data': []})
        
        data = pola_harian[pola_harian['Laboratorium'] == ruangan]
        
        if data.empty:
            return jsonify({'success': True, 'nama_ruangan': ruangan, 'heatmap_data': []})
        
        heatmap_data = []
        for _, row in data.iterrows():
            heatmap_data.append({
                'jam': int(row['Jam_Mulai_Int']),
                'frekuensi': int(row['Frekuensi'])
            })
        
        # Urutkan berdasarkan jam
        heatmap_data.sort(key=lambda x: x['jam'])
        
        return jsonify({
            'success': True,
            'nama_ruangan': ruangan,
            'heatmap_data': heatmap_data
        })
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# ============================================
# ENDPOINT 7: REKOMENDASI RUANGAN KOSONG
# ============================================
@app.route('/predict/ruangan', methods=['POST'])
def recommend_ruangan():
    """
    Rekomendasi ketersediaan ruangan berdasarkan pola historis
    
    Request body:
    {
        "laboratorium": "Laboratorium KMB/DKKD",
        "hari_int": 0,
        "jam_int": 10
    }
    
    Response:
    {
        "status": "tersedia",
        "rekomendasi_jam": null
    }
    atau
    {
        "status": "penuh",
        "rekomendasi_jam": 15
    }
    """
    try:
        data = request.json
        if not data:
            return jsonify({'error': 'No data provided'}), 400
        
        nama_lab = data.get('laboratorium')
        target_hari = data.get('hari_int')
        target_jam = data.get('jam_int')
        
        if not nama_lab or target_hari is None or target_jam is None:
            return jsonify({'error': 'laboratorium, hari_int, jam_int are required'}), 400
        
        if kepadatan_df.empty:
            return jsonify({'status': 'tersedia', 'rekomendasi_jam': None})
        
        # Filter data historis untuk lab dan hari tertentu
        data_hari_ini = kepadatan_df[
            (kepadatan_df['Laboratorium'] == nama_lab) & 
            (kepadatan_df['Hari_Int'] == target_hari)
        ]
        
        # Cek apakah jam yang diminta padat
        cek_jadwal = data_hari_ini[data_hari_ini['Jam_Mulai_Int'] == target_jam]
        
        # Ambang batas kepadatan (frekuensi > 2 dianggap penuh)
        if not cek_jadwal.empty and cek_jadwal['Frekuensi'].values[0] > 2:
            # Cari jam kosong alternatif
            jam_sibuk = data_hari_ini['Jam_Mulai_Int'].tolist()
            jam_operasional = list(range(8, 17))  # 08:00 - 16:00
            jam_kosong = [j for j in jam_operasional if j not in jam_sibuk]
            
            if jam_kosong:
                # Cari jam yang paling dekat dengan target
                jam_rekomendasi = min(jam_kosong, key=lambda x: abs(x - target_jam))
                return jsonify({'status': 'penuh', 'rekomendasi_jam': jam_rekomendasi})
            else:
                return jsonify({'status': 'penuh_sepanjang_hari', 'rekomendasi_jam': None})
        else:
            return jsonify({'status': 'tersedia', 'rekomendasi_jam': None})
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# ============================================
# ENDPOINT 8: DAFTAR SEMUA BARANG
# ============================================
@app.route('/master/barang', methods=['GET'])
def get_master_barang():
    """
    Mendapatkan daftar semua alat dan bahan
    
    Query params:
    - tipe: alat/bahan (opsional)
    - search: keyword pencarian (opsional)
    """
    try:
        tipe = request.args.get('tipe')
        search = request.args.get('search', '').lower()
        
        if master_barang.empty:
            return jsonify({'success': True, 'items': []})
        
        data = master_barang.copy()
        
        # Filter tipe
        if tipe and tipe in ['alat', 'bahan']:
            data = data[data['tipe'] == tipe]
        
        # Filter pencarian
        if search:
            data = data[data['nama_barang'].str.lower().str.contains(search, na=False)]
        
        items = []
        for _, row in data.iterrows():
            items.append({
                'id_barang': row.get('id_barang', ''),
                'nama_barang': row['nama_barang'],
                'tipe': row.get('tipe', 'alat'),
                'rata_rata_per_hari': round(row.get('rata_rata_per_hari', 0), 2)
            })
        
        return jsonify({
            'success': True,
            'total': len(items),
            'items': items
        })
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# ============================================
# ENDPOINT 9: DAFTAR SEMUA RUANGAN
# ============================================
@app.route('/master/ruangan', methods=['GET'])
def get_master_ruangan():
    """
    Mendapatkan daftar semua ruangan laboratorium
    """
    try:
        if kepadatan_df.empty:
            return jsonify({'success': True, 'ruangan': []})
        
        ruangan_list = kepadatan_df['Laboratorium'].unique().tolist()
        
        return jsonify({
            'success': True,
            'total': len(ruangan_list),
            'ruangan': ruangan_list
        })
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# ============================================
# MAIN
# ============================================
if __name__ == '__main__':
    port = int(os.environ.get('PORT', 5000))
    debug = os.environ.get('DEBUG', 'True').lower() == 'true'
    app.run(host='0.0.0.0', port=port, debug=debug)