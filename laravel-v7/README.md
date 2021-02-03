## Upgrade guide

```shell script
# Import `permisions` and `roles` tables via `new-db.sql` file
mysql -uUserName -pPassword dbname < new-db.sql

# Create default data with below comands
php artisan vendor:publish --force
php artisan db:seed --class=TNTT\\Database\\Seeds\\DatabaseSeeder
```

## Todo List
- Model: remove constructures, optimize relationship (morph...)
- Time: Carbon time
- Remove old/unuse tables in databases.
    ```shell script
    drop table phanquyen_nhomtaikhoan;
    drop table taikhoan_nhomtaikhoan;
    drop table phan_quyen;
    drop table nhom_tai_khoan;
    drop table migrations;
    drop table git_webhooks;
    drop table lien_ket;
    ```
