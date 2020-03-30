## Developing

- This version is building ... 

## To Do list

- Model: remove constructures, optimize relationship (morph...)
- Time: Carbon time
- Logout: add front-end
- Cache: default file
- Remove old/unuse tables in databases.
    - phan_quyen
    - phanquyen_nhomtaikhoan
    - taikhoan_nhomtaikhoan
    - nhom_tai_khoan
    - migrations
    - git_webhooks
    - gia_pha
    - lien_ket
    - than_nhan
- Customize permisison and role
```json
{
  "data": [
    {
      "id": 1,
      "ten": "tai-khoan",
      "ten_hien_thi": "Tài Khoản",
      "ghi_chu": "- Chỉnh sửa thông tin Tài Khoản\n- Thay đổi mật khẩu",
      "created_at": "2015-07-19 07:08:30",
      "updated_at": "2017-11-20 22:24:12",
      "role_nhom": [
        {
          "id": 1,
          "loai": "NHOM",
          "ten": "Ban Học Tập",
          "ten_hien_thi": "Ban Học Tập",
          "ghi_chu": "Ban Học Tập",
          "created_at": "2015-07-19 07:08:30",
          "updated_at": "2015-07-19 07:08:30",
          "pivot": {
            "phan_quyen_id": 1,
            "nhom_tai_khoan_id": 1
          }
        }
      ],
      "role_taikhoan": []
    }
  ]
}
```

- Export excel