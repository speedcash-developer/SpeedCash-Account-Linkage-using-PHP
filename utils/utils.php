<?php

function dateTime() {
    $dateTime = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
    writeLocalData(['timeStamp' => $dateTime->format(DateTime::ATOM)]);
    return $dateTime->format(DateTime::ATOM);
}

function generateToken($length) {
    $token = bin2hex(random_bytes($length)); 
    return $token;
}

function readLocalData() {
    try {
        // Cek apakah file ada
        if (!file_exists('localData.json')) {
            return []; // Jika file tidak ada, kembalikan array kosong
        }

        // Baca konten file
        $data = file_get_contents('localData.json');
        
        // Decode JSON menjadi array
        $decodedData = json_decode($data, true);

        // Jika JSON tidak valid, kembalikan array kosong
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON format: ' . json_last_error_msg());
        }

        return $decodedData ?: [];
    } catch (Exception $error) {
        // Log error jika ada masalah
        error_log('Gagal membaca data di lokal: ' . $error->getMessage());
        throw $error; // Lempar ulang error jika terjadi masalah selain file tidak ada
    }
}

function writeLocalData($newData) {
    try {
        // Baca data yang sudah ada
        $existingData = readLocalData();

        // Gabungkan data lama dengan data baru
        $updatedData = array_merge($existingData, $newData);

        // Tulis data ke file localData.json
        $json = json_encode($updatedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents('localData.json', $json);

        return true;
    } catch (Exception $error) {
        error_log('Gagal menyimpan data di lokal: ' . $error->getMessage());
        return false;
    }
}
