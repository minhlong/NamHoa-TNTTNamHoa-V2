import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { ToasterService } from 'angular2-toaster';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

import { JwtAuthHttp } from '../../../services/http-auth.service';
import { environment } from './../../../../environments/environment';
import { ngay } from '../../shared/convert-type.pipe';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrls: ['./form.component.scss']
})
export class FormComponent implements OnInit {
  private urlAPI = environment.apiURL + '/tai-khoan';

  @Input() taiKhoanInfo: any;
  @Input() curAuth: any;
  @Output() updateInfo = new EventEmitter();

  isLoading: boolean;
  infoFB: FormGroup;
  error: any;
  maskOption = {
    mask: [/[0-3]/, /[0-9]/, '-', /[0-1]/, /[0-9]/, '-', /[1-2]/, /[0-9]/, /[0-9]/, /[0-9]/],
    guide: true,
  }

  constructor(
    private toasterService: ToasterService,
    private formBuilder: FormBuilder,
    private _http: JwtAuthHttp,
  ) {
  }

  ngOnInit() {
    this.infoFB = this.formBuilder.group({
      id: this.taiKhoanInfo.id,
      ten_thanh: this.taiKhoanInfo.ten_thanh,
      ho_va_ten: this.taiKhoanInfo.ho_va_ten,
      trang_thai: this.taiKhoanInfo.trang_thai,
      loai_tai_khoan: this.taiKhoanInfo.loai_tai_khoan,
      gioi_tinh: this.taiKhoanInfo.gioi_tinh,
      ngay_sinh: ngay(this.taiKhoanInfo.ngay_sinh),
      ngay_rua_toi: ngay(this.taiKhoanInfo.ngay_rua_toi),
      ngay_ruoc_le: ngay(this.taiKhoanInfo.ngay_ruoc_le),
      ngay_them_suc: ngay(this.taiKhoanInfo.ngay_them_suc),
      email: this.taiKhoanInfo.email,
      dien_thoai: this.taiKhoanInfo.dien_thoai,
      dia_chi: this.taiKhoanInfo.dia_chi,
      ghi_chu: this.taiKhoanInfo.ghi_chu,
    });
  }

  hasPermBHT() {
    // Quyền này để update trạng thái và loại tài khoản,
    // Không phải ai cũng có thể cập nhật được 2 điều này
    if (this.curAuth.phan_quyen.includes('tai-khoan')) {
      return true;
    }
    return false;
  }

  save() {
    const _url = this.urlAPI + '/' + this.taiKhoanInfo.id;
    const _par = Object.assign({}, this.infoFB.value, {
      ngay_sinh: ngay(this.infoFB.value.ngay_sinh),
      ngay_rua_toi: ngay(this.infoFB.value.ngay_rua_toi),
      ngay_ruoc_le: ngay(this.infoFB.value.ngay_ruoc_le),
      ngay_them_suc: ngay(this.infoFB.value.ngay_them_suc),
    });

    this.isLoading = true;
    this._http.post(_url, _par).map(res => res.json()).subscribe(res => {
      this.isLoading = false;
      this.updateInfo.emit(res);
    }, _err => {
      this.isLoading = false;
      if (typeof _err === 'string') {
        this.toasterService.pop('error', 'Lỗi!', _err);
      } else {
        this.error = _err;
        for (const _field in _err) {
          if (_err.hasOwnProperty(_field)) {
            _err[_field].forEach(_mess => {
              this.toasterService.pop('error', 'Lỗi!', _mess);
            });
          }
        }
      }
    });
  }

  cancel() {
    this.updateInfo.emit(null);
  }
}
