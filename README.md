# About

Repository ini berisi PHP dan Terraform script untuk membangun API Terbilang.

Untuk panduan lengkap silahkan merujuk pada artikel TeknoCerdas.com berikut.

- [Serverless PHP: Membuat API Terbilang dengan Lambda
](https://teknocerdas.com/programming/serverless-php-membuat-api-terbilang-dengan-lambda/)

## How to Run

Pastikan anda memiliki AWS account dengan privilege Administrator agar tidak terjadi masalah ketika menjalankan Terraform.

```
$ export AWS_PROFILE=YOUR_PROFILE AWS_DEFAULT_REGION=YOUR_REGION
```

Build API script terlebih dahulu.

```
$ bash build.sh
```

Masuk pada directory `terraform/` dan mulai jalankan init dan apply untuk membuat resources yang diperlukan.

```
$ terraform init
$ terraform apply
```

```
...
Outputs:

api = {
  "end_point" = "GET https://3rj954v9wd.execute-api.us-east-1.amazonaws.com/myip"
}
```

Uji coba API terbilang menggunakan cURL.

```
$ curl -s -H "Content-Type: application/json" "https://3rj954v9wd.execute-api.us-east-1.amazonaws.com/terbilang?angka=$RANDOM&pretty"
{
    "angka": "24045",
    "terbilang": "dua puluh empat ribu empat puluh lima"
}
```

## License

Repository ini dilensiskan dibawah naungan [MIT License](https://opensource.org/licenses/MIT).
