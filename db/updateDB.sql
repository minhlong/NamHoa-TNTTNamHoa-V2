SET sql_mode = '';

ALTER TABLE taikhoan_lophoc MODIFY created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL;
ALTER TABLE taikhoan_lophoc MODIFY updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL;
ALTER TABLE taikhoan_lophoc MODIFY chuyen_can double(5,2) DEFAULT 10 NOT NULL;
ALTER TABLE taikhoan_lophoc MODIFY hoc_luc double(5,2) DEFAULT 10 NOT NULL;

delete from diem_danh where phan_loai = 'LOGS';
delete from diem_so where phan_loai = 'LOGS';


-- DESCRIBE taikhoan_lophoc;
-- update tai_khoan set mat_khau = '$2y$10$sEwzwosAXoJe3Jo0yX4pf.1oOK5jj6.1BoP4niOJcq4ztVSYXYZWG';