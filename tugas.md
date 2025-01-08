Pendahuluan



API
adalah sekumpulan aturan dan mekanisme yang memungkinkan dua aplikasi
atau sistem untuk berkomunikasi satu sama lain. API bertindak sebagai
jembatan penghubung antara aplikasi frontend (pengguna) dengan
backend (server) atau antara aplikasi yang berbeda. API ini akan
sangat berguna dalam hal:    


    Mempermudah integrasi antar sistem.   


    Mendukung pengembangan aplikasi yang modular.   


    Memungkinkan pengembang untuk menggunakan layanan pihak ketiga.




Laravel
adalah framework PHP yang sangat populer untuk pengembangan API
karena memiliki fitur-fitur berikut:


    Routing:
    Laravel menyediakan routing yang mudah digunakan untuk mengatur
    endpoint API. 


    Eloquent
    ORM:
    Membantu dalam manajemen database untuk memanipulasi data dengan
    cara yang efisien.


    Validation:
    Laravel memiliki fitur validasi data yang kuat untuk memastikan data
    yang dikirim ke API sesuai dengan aturan.


    Laravel
    HTTP Client:
    Fitur bawaan untuk membuat request ke API pihak ketiga.


    Middleware:
    Membantu dalam mengelola otorisasi, autentikasi, atau manipulasi
    data request dan response.


    Testing:
    Laravel menyediakan alat untuk menguji API yang dikembangkan


HTTP
Request adalah permintaan yang dikirimkan dari klien (seperti browser
atau aplikasi) ke server untuk meminta data atau melakukan tindakan
tertentu.



Adapun
komponen utama HTTP request:


    Method:
    Menentukan jenis aksi yang akan dilakukan (GET, POST, PUT, DELETE,
    dsb.).


    URL:
    Alamat sumber daya (endpoint API) yang diminta.


    Headers:
    Informasi tambahan seperti otentikasi, tipe konten, dll.


    Body:
    Data yang dikirimkan dalam request (biasanya pada POST atau PUT).




Penjelasan
HTTP Request (GET, POST, PUT, DELETE)

GET:
    digunakan
    untuk mengambil data dari server, Tidak memiliki body (data dikirim
    melalui URL atau query parameters), pada umumnya, metode ini
    digunakan ketika kita ingin mengambil daftar pengguna atau
    mendapatkan detail suatu artikel.

POST:
    digunakan
    untuk mengirim data ke server (biasanya untuk menyimpan data baru).
    Data    dikirimkan dalam body request.
    Dan
    digunakan ketika membuat akun baru atau menambahkan komentar pada
    sebuah postingan.

PUT:
    digunakan
    untuk memperbarui data di server, digunakan untuk mengganti seluruh
    data dari suatu resource.
    Sama
    seperti post, data dikirimkan dalam body request.

DELETE:
    digunakan untuk menghapus data di server dan proses request biasanya
    tidak memiliki body.




HTTP
Response



HTTP
Response
adalah hasil
yang didapatkan oleh kita saat melakukan request ke API menggunakan
HTTP Request. Terdapat beberapa hal yang perlu diketahui dari HTTP
Response, yaitu: 



Status
Codes



Status
codes adalah angka tiga digit yang menunjukkan status dari HTTP
response.



Kategori
Status Codes:   


    1xx:
    Informational
    (Informasi)


        Contoh: 100 Continue (server menerima request awal)


    2xx:
    Success
    (Berhasil)


        200 OK: Permintaan berhasil.


        201 Created: Data baru berhasil dibuat.


    3xx:
    Redirection
    (Redirect)


        301 Moved Permanently: Resource telah dipindahkan ke URL baru.


    4xx:
    Client Error
    (Kesalahan dari klien)


        400 Bad Request: Permintaan tidak valid.


        401Unauthorized: Permintaan membutuhkan otentikasi.


        404 Not Found: Resource tidak ditemukan.


    5xx:
    Server Error
    (Kesalahan dari server)


        500 Internal Server Error: Kesalahan internal server.




Body:
Body
adalah bagian dari HTTP response yang berisi data sebenarnya,
biasanya dalam format JSON atau XML.

Bagaimana cara melakukan request?

Untuk melakukan request ke API, hal yang perlu dipersiapkan adalah url endpoints dari API yang ingin digunakan. Contoh, ketika kita ingin menggunakan API untuk mendapatkan data beans, dapat menggunakan 

https://jellybellywikiapi.onrender.com/api/beans

hal ini sesuai dengan ketentuan pada sumbernya, yaitu pada halaman berikut: https://jelly-belly-wiki.netlify.app/api/ 

setelah disiapkan, persiapkan route pada laravel terlebih dahulu, dan arahkan ke controller yang sesuai. contoh: 

Route::get('/', [FoodController::class, 'index']);

Setelah itu, buat controller yang telah ditentukan pada route dan methodnya. contoh: 

class FoodController extends Controller
{
      public function index() {
            $response = Http::get(env('API_URL'));
            $data = $response->json();
            return view('index', [
                  'title' => 'Beans',
                  'datas' => $data['items']
            ]);
      }
}

buatlah view sesuai dengan yang telah ditentukan oleh controller, dan buatlah tampilan visual sesuai dengan API yang telah didapatkan. 

pada kasus yang telah diberikan, ada masanya kita ingin memberikan konfigurasi secara global untuk API_URL atau endpoints yang akan digunakan. Untuk itu, kita bisa memberikan data pada file .env, dengan variabel API_URL (pada contoh diatas, kita bisa kustomisasi nama variabelnya) dan berikan endpoints pada variabel tersebut. untuk pengaksesannya, kita dapat menggunakan variabel env('.env variabel key'). pada contoh diatas, dikarenakan kita menambahkan API_URL pada env sebagai endpoints dari API, maka pengaksesannya dapat menggunakan env('API_URL'). 

API_URL=https://jellybellywikiapi.onrender.com/api/beans

API CRUD

Konsep API juga dapat kita gunakan pada umumnya dalam penerapan Create, Read, Update, Delete (CRUD). Pada kasus yang diberikan seperti ada response dan sebagainya merupakan bagian dari Read Request API. Create, Update, Delete dalam API pada umumnya digunakan oleh beberapa developer dalam memanfaatkan pengembangan website yang menggunakan bahasa atau framework yang berbeda. Seperti frontend dengan javascript backend dengan PHP, dan sebagainya. Hal yang dilakukan sama, adapun hal tersebut akan kita pelajari seperti berikut

Buat folder Api dalam folder app/Http/Controllers. 

Buat folder Web dalam folder app/Http/Web

Buat tampilan view dari suatu project pada resources/views

Setting database di .env. Contoh: 

DB_CONNECTION=mysql

DB_HOST=127.0.0.1

DB_PORT=5432

DB_DATABASE=api_exercise

DB_USERNAME=root

DB_PASSWORD=

Buat model dengan: php artisan make:model NamaModel

Buka file database/migrations/…create_namamodel_table.php

edit lah kolom table pada Schema::create. Contoh: 

public function up(): void

    {

        Schema::create('pegawais', function (Blueprint $table) {

            $table->id();

            $table->string('name');

            $table->integer('usia');

            $table->integer('masa_kerja');

            $table->float('gaji');

            $table->timestamps();

        });

    }

kemudian, jalankan php artisan migrate untuk menghasilkan table ke database (pastikan nama database yang ada di .env telah dibuat sebelumnya)

buatlah resource dengan menggunakan php artisan make:resource NamaResource diterminal

Lalu, tambahkan kode untuk menerima dan mengirim output API pada resources. Contoh: 

        public $status;

        public $message;

        public $resource;

        public $collects = Pegawai::class;


        public function __construct($status, $message, $resource) {

            parent::__construct($resource);

            $this->status = $status;

            $this->message = $message;

        }


        public function toArray(Request $request): array

        {

              return [

                 'success' => $this->status,

                 'message' => $this->message, 

                 'data' => $this->resource

             ];

         }

pada route, untuk membuat request ke API dengan resource seperti diatas, akan mengeluarkan output JSON saat direquest. tambahkan resource ini ke Api.php seperti berikut: 

     // route request all
     Route::get('/', function() {

         return Pegawai::all();

     });

    // route request create

     Route::post('/save', function(Request $request) {

         return Pegawai::create($request->all());

      });

     buatlah kode untuk melakukan request untuk mendapatkan data pada halaman home. hal ini dapat   dilakukan seperti contoh berikut: 

      public function index() {

            $response = Http::get('http://127.0.0.1:8000/api');

            $data = $response->json();


             return view('home', [

                    'data' => $data

             ]);

       }

buat lah view home dan kemudian buat method untuk melakukan CRUD API. contoh dalam kasus ini, akan dibuat method untuk melakukan penambahan data

       public function save(Request $request) {

             $response = Http::post('http://127.0.0.1:8000/api/save', $request->all());

              return redirect()->route('home');

       }


API CRUD Berhasil dibuat