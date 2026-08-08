# Order Management System (OMS) - Testing System Development Supervisor

Sistem Order Management berbasis Laravel 5.8 yang mendukung integrasi Public API (produk, shipping, payment), order lifecycle, async processing via queue, event-driven architecture, dan concurrency handling.

## Daftar Isi

- [Fitur Utama]
- [Arsitektur]
- [Requirement]
- [Instalasi]
- [Menjalankan Aplikasi]
- [Order Lifecycle]
- [Integrasi Public API]
- [Testing Alur Lengkap]
- [Struktur Folder]

## Fitur Utama

- Product Aggregation — fetch produk dari FakeStoreAPI dengan cache (5 menit) dan fallback saat API gagal
- Order Processing — pembuatan order, snapshot harga produk, kalkulasi total + ongkir otomatis sesuai pilihan
- Payment Handling (Async) — payment request via queue, simulasi webhook callback (success/failed)
- Shipping Integration — kalkulasi ongkir per kurir (JNE, J&T, SiCepat) via simulasi RajaOngkir
- Queue System — payment processing, notifikasi email, semuanya asynchronous
- Event-Driven — OrderCreated, PaymentSucceeded, PaymentFailed, OrderStatusUpdated
- Concurrency Handling — idempotency key dan row-locking 
- Logging & Monitoring — semua external API call & request API tercatat terstruktur
- Web Interface — halaman produk, keranjang belanja, checkout, tracking order, riwayat order
- Authentication — register/login dengan field tambahan (username, alamat, usia, jenis kelamin)
- Email Notification — email otomatis saat pembayaran diterima dan saat pesanan sampai lewat Mailbox belum sampai masuk ke gmail

## Arsitektur

```
Controller → Service Layer -> Repository (Interface) -> Eloquent Model / API Client
                   
              Event Dispatch
                    
              Listener -> Job (Queue) -> Mail / External Call
```

- PHP >= 7.1.3
- Composer
- PostgreSQL
- Extension PHP: `pdo`, `mbstring`, `openssl`, `curl`

## Instalasi

```bash
# 1. Clone repository
git clone <repository-url>
cd oms

# 2. Install dependency
composer install

# 3. Copy environment file
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=oms
DB_USERNAME=postgres
DB_PASSWORD=

# 5. Konfigurasi queue (database driver)
QUEUE_CONNECTION=database

# 6. Konfigurasi mail (opsional — untuk notifikasi email)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls

# 7. Jalankan migration
php artisan migrate

# 8. Generate autoload
composer dump-autoload
```

## Menjalankan Aplikasi

Aplikasi membutuhkan 3 proses berjalan bersamaan:

# Terminal 1 — HTTP Server
php artisan serve

# Terminal 2 — Queue Worker (wajib untuk payment & email async)
php artisan queue:work

# Terminal 3 — bebas untuk tinker/testing manual
php artisan tinker


Buka http://localhost:8000 di browser untuk mengakses web interface.

## Order Lifecycle


CREATED -> PENDING_PAYMENT -> PAID -> PACKING -> IN_WAREHOUSE -> ON_DELIVERY -> DELIVERED
                -> FAILED / CANCELLED

Transisi status divalidasi lewat state machine di App\Order::canTransitionTo() — mencegah lompatan status yang tidak valid (misal CREATED langsung ke DELIVERED).

## Integrasi Public API
| Kategori | Provider | Jenis |
|---|---|---|
| Product API | [FakeStoreAPI](https://fakestoreapi.com/)
| Shipping API | RajaOngkir | 
| Payment API | Mock Payment Gateway | Simulasi |

Tarif ongkir simulasi: JNE $1.00, J&T $1.20, SiCepat $1.30. Karena Harga produk dolar

## Testing Alur Lengkap

### 1. Buat order (via web atau tinker)

$service = app(\App\Services\OrderService::class);

$order = $service->createOrder([
    'idempotency_key' => (string) \Illuminate\Support\Str::uuid(),
    'user_id' => 1,
    'items' => [['product_id' => 1, 'quantity' => 2]],
    'shipping' => ['destination' => 'Jakarta', 'courier' => 'jne'],
]);
```

### 2. Ambil reference payment

\App\Payment::where('order_id', $order->id)->first()->external_reference;

### 3. Simulasi webhook payment sukses (via Postman)

POST /api/webhooks/payment
{
    "reference": "PAY-XXXXXXXXXX",
    "status": "SUCCESS"
}

### 4. Update status pengiriman (via web interface)

Buka halaman detail order (`/orders/{order_number}`), klik tombol update status berturut-turut: PACKING -> IN_WAREHOUSE -> ON_DELIVERY -> DELIVERED.

### 5. Cek notifikasi email
Email otomatis terkirim saat status PAID dan DELIVERED — cek dashboard Mailtrap (jika `MAIL_MAILER=smtp`) atau `storage/logs/laravel.log` (jika `MAIL_MAILER=log`).

## Known Limitations

- Payment gateway dan shipping cost bersifat simulasi
- Tracking number pengiriman belum terintegrasi dengan API tracking real-time
- Email menggunakan sandbox testing (Mailtrap) secara default
