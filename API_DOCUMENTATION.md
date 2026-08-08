# API Documentation — Order Management System

Base URL: http://localhost:8000/api

Semua response berformat JSON. Semua request `POST` menggunakan `Content-Type: application/json`.

## Daftar Endpoint

| Method | Endpoint | Deskripsi |
| POST | `/orders` | Membuat order baru |
| GET | `/orders/{orderNumber}` | Mengambil detail order |
| POST | `/webhooks/payment` | Simulasi callback payment gateway |

## 1. Create Order

Membuat order baru. Bersifat idempotent — mengirim `idempotency_key` yang sama akan mengembalikan order yang sudah ada, bukan membuat duplikat.

POST /api/orders

Request Body
{
    "idempotency_key": "unique-key-123",
    "user_id": 1,
    "items": [
        {
            "product_id": 1,
            "quantity": 2
        },
        {
            "product_id": 3,
            "quantity": 1
        }
    ],
    "shipping": {
        "destination": "Jakarta",
        "courier": "jne"
    }
}



**Response — 201 Created**
```json
{
    "message": "Order created successfully",
    "data": {
        "id": 5,
        "order_number": "6200d7eb-30ec-4329-911c-f5af0932dd68",
        "idempotency_key": "unique-key-123",
        "status": "CREATED",
        "subtotal": "219.90",
        "shipping_cost": "1.00",
        "total": "220.90",
        "items": [
            {
                "external_product_id": "1",
                "product_name": "Fjallraven Backpack",
                "price": "109.95",
                "quantity": 2,
                "subtotal": "219.90"
            }
        ],
        "shipment": {
            "courier": "jne",
            "service": "REG",
            "cost": "1.00",
            "status": "PENDING"
        }
    }
}
```

**Response — 422 Unprocessable Entity** (stok tidak cukup / produk tidak ditemukan / validasi gagal)
```json
{
    "message": "Failed to create order",
    "error": "Insufficient stock for product 1. Available: 5, requested: 10"
}
```

---

## 2. Get Order Detail

Mengambil detail order beserta items, payment, dan shipment.

**Endpoint**
```
GET /api/orders/{orderNumber}
```

**Path Parameter**

| Parameter | Deskripsi |
|---|---|
| `orderNumber` | UUID order (bukan `id` internal) |

**Response — 200 OK**
```json
{
    "data": {
        "id": 5,
        "order_number": "6200d7eb-30ec-4329-911c-f5af0932dd68",
        "status": "PAID",
        "subtotal": "219.90",
        "shipping_cost": "1.00",
        "total": "220.90",
        "items": [...],
        "payment": {
            "status": "SUCCESS",
            "external_reference": "PAY-0I2UNBOBZS",
            "paid_at": "2026-08-07 03:15:42"
        },
        "shipment": {
            "courier": "jne",
            "status": "PENDING"
        }
    }
}
```

**Response — 404 Not Found**
```json
{
    "message": "No query results for model [App\\Order]."
}
```

---

## 3. Payment Webhook

Endpoint simulasi callback dari payment gateway. Digunakan untuk mengubah status pembayaran dan memicu event `PaymentSucceeded`/`PaymentFailed`.

**Endpoint**
```
POST /api/webhooks/payment
```

**Request Body**
```json
{
    "reference": "PAY-0I2UNBOBZS",
    "status": "SUCCESS"
}
```


**Response — 200 OK**
```json
{
    "message": "Webhook processed",
    "data": {
        "id": 1,
        "status": "SUCCESS",
        "paid_at": "2026-08-07 03:15:42",
        "order": {
            "id": 5,
            "status": "PAID"
        }
    }
}
```

**Idempotency Note:** Jika webhook dengan `reference` yang sama dikirim ulang setelah status sebelumnya sudah diproses (bukan `PENDING`), sistem akan mengembalikan data payment tanpa memproses ulang — mencegah efek ganda dari webhook duplikat.

**Response — 404 Not Found** (reference tidak ditemukan)
```json
{
    "message": "No query results for model [App\\Payment]."
}
```

---

## Error Handling
Semua error mengikuti format standar Laravel:

| Status Code | Arti |
|---|---|
| 200 | Sukses |
| 201 | Resource berhasil dibuat |
| 404 | Resource tidak ditemukan |
| 422 | Validasi gagal / business logic error |
| 500 | Server error (cek `storage/logs/laravel.log`) |