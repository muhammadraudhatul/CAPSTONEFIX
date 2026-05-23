from flask import Flask, request, jsonify
import xgboost as xgb
import pandas as pd

app = Flask(__name__)

# Load Model XGBoost
model_alat = xgb.XGBRegressor()
model_alat.load_model('model_alat.json')

model_bahan = xgb.XGBRegressor()
model_bahan.load_model('model_bahan.json')

# Load Data Ruangan
kepadatan_df = pd.read_csv('data_kepadatan_ruangan.csv')

@app.route('/predict/stok', methods=['POST'])
def predict_stok():
    data = request.json
    fitur = [[
        data['Hari'], data['Bulan'], data['Minggu_Ke'], data['Jumlah_Praktikum'], data['Status_Ujian'],
        data['Lag_1'], data['Lag_7'], data['Roll_Mean_3']
    ]]
    if data['tipe'] == 'alat':
        pred = model_alat.predict(fitur)[0]
    else:
        pred = model_bahan.predict(fitur)[0]
    return jsonify({'prediksi': max(round(float(pred)), 0)})

@app.route('/predict/ruangan', methods=['POST'])
def recommend_ruangan():
    data = request.json
    nama_lab = data['laboratorium']
    target_hari = data['hari_int']
    target_jam = data['jam_int']

    data_hari_ini = kepadatan_df[(kepadatan_df['Laboratorium'] == nama_lab) & (kepadatan_df['Hari_Int'] == target_hari)]
    cek_jadwal = data_hari_ini[data_hari_ini['Jam_Mulai_Int'] == target_jam]

    if not cek_jadwal.empty and cek_jadwal['Frekuensi'].values[0] > 1:
        jam_sibuk = data_hari_ini['Jam_Mulai_Int'].tolist()
        jam_kosong = [j for j in range(8, 17) if j not in jam_sibuk]
        jam_rekomendasi = min(jam_kosong, key=lambda x: abs(x - target_jam)) if jam_kosong else None
        return jsonify({'status': 'penuh', 'rekomendasi_jam': jam_rekomendasi})
    return jsonify({'status': 'tersedia'})

if __name__ == '__main__':
    app.run(port=5000, debug=True)