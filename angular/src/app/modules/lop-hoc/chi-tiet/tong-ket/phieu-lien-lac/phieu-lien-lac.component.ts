import { Component, OnInit, Input, Output, EventEmitter, OnDestroy } from '@angular/core';
import { Store } from '@ngrx/store';

import { AppState } from '../../../../../store/reducers';
import { ngay } from '../../../../shared/utities.pipe';

@Component({
  selector: 'app-phieu-lien-lac',
  templateUrl: './phieu-lien-lac.component.html',
  styleUrls: ['./phieu-lien-lac.component.scss']
})
export class PhieuLienLacComponent implements OnInit, OnDestroy {
  @Input() apiData;
  @Output() updateInfo = new EventEmitter();

  logoImg = 'http://tnttnamhoa.org/assets/images/logo.png';
  chucDanhLinhMuc = 'LM Chánh Xứ';
  linhMuc = 'Phanxico Xavier Đậu Nguyễn Hoàng Linh';
  linhMucImg = 'http://tnttnamhoa.org/assets/images/sign-Cha.png';

  xuDoanTruong = 'Gioan Baotixita Hoàng Quang Vũ';
  doanTruongImg = 'http://tnttnamhoa.org/assets/images/sign-AVu.png';

  htPhuTrach: string;
  ngayXetDuyet: string;

  chCanArr = [];
  lopHocInfo: any = {};
  khoaHienTai: any = {};
  pagingTN = {
    id: 'tnTable',
    itemsPerPage: 10,
    currentPage: 1,
  }
  sub$: any;
  subKhoaHoc$: any;

  constructor(
    private store: Store<AppState>,
  ) {
    this.ngayXetDuyet = 'TP HCM, Ngày ' + ngay(new Date().toJSON().slice(0, 10));

    this.sub$ = this.store.select((state: AppState) => state.lop_hoc.thong_tin).subscribe(res => {
      this.lopHocInfo = res;

      // Lấy thông tin huynh trưởng đầu tiên của lớp để in Phiếu Liên Lạc
      if (res && res.huynh_truong && res.huynh_truong.length) {
        const tmpArr = [];
        res.huynh_truong.forEach(el => {
          let ten = '';
          if (el.loai_tai_khoan === 'HUYNH_TRUONG') {
            if (el.gioi_tinh === 'NU') {
              ten += 'Chị ';
            } else if (el.gioi_tinh === 'NAM') {
              ten += 'Anh ';
            }
          } else if (el.loai_tai_khoan === 'SOEUR') {
            ten += 'Sơ ';
          }

          ten += el.ten_thanh + ' ' + el.ho_va_ten;
          tmpArr.push(ten);
        });
        this.htPhuTrach = tmpArr.join('; ');
      }
    });

    // Lấy thông số cơ cấu điểm và ràng buộc từ thông tin khóa học
    this.subKhoaHoc$ = this.store.select((state: AppState) => state.auth.khoa_hoc_hien_tai).subscribe(res => {
      this.khoaHienTai = res;
    });
  }

  ngOnInit() {
    // Thống Kê chuyên cần - Số lần đi lễ - đi học
    if (this.apiData && this.apiData.DiemDanh) {
      const ngayArr = Object.keys(this.apiData.DiemDanh);
      ngayArr.forEach(_ngay => {
        if (this.apiData.DiemDanh[_ngay]) {
          const tnArr = Object.keys(this.apiData.DiemDanh[_ngay]);
          tnArr.forEach(_id => {
            if (this.apiData.DiemDanh[_ngay][_id]) {
              const _phieu = this.apiData.DiemDanh[_ngay][_id];
              if (_phieu.di_le) {
                this.chCanArr.push({
                  id: _id,
                  loai: 'di_le',
                  ngay: _ngay,
                  phieu: _phieu.di_le,
                });
              }
              if (_phieu.di_hoc) {
                this.chCanArr.push({
                  id: _id,
                  loai: 'di_hoc',
                  ngay: _ngay,
                  phieu: _phieu.di_hoc,
                });
              }
            }
          });
        }
      });
    }
  }

  ngOnDestroy() {
    this.sub$.unsubscribe();
    this.subKhoaHoc$.unsubscribe();
  }

  cancel() {
    this.updateInfo.emit(null);
  }

  print() {
    window.print();
  }

  thongKePhieu(_tn, _loai, _phieu) {
    const _count = this.countPhieu(_tn, _loai, _phieu);
    let _minus = 0;
    if (this.khoaHienTai[_loai][_phieu]) {
      _minus = _count * this.khoaHienTai[_loai][_phieu];
    }
    return _count + ' | ' + (Math.round(_minus * 100) / 100);
  }

  private countPhieu(_tn, _loai, _phieu) {
    return this.chCanArr.filter(c =>
      c.id === _tn.id &&
      c.loai === _loai &&
      c.phieu === _phieu).length
  }

  tbCaNam(_tn) {
    return Math.round(_tn.pivot.tb_canam * 100) / 100;
  }
}
