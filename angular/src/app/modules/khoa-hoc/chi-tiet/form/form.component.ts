import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { ToasterService } from 'angular2-toaster';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

import { JwtAuthHttp } from './../../../../services/http-auth.service';
import { environment } from './../../../../../environments/environment';
import { ngay } from './../../../shared/convert-type.pipe';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrls: ['./form.component.scss'],
})
export class FormComponent implements OnInit {
  private urlAPI = environment.apiURL + '/khoa-hoc';

  @Input() khoaInfo: any;
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
      id: this.khoaInfo.id,
      ngay_bat_dau: this.khoaInfo.ngay_bat_dau,
      ngay_ket_thuc: this.khoaInfo.ngay_ket_thuc,
      so_dot_kiem_tra: this.khoaInfo.so_dot_kiem_tra,
      so_lan_kiem_tra: this.khoaInfo.so_lan_kiem_tra,
      cap_nhat_dot_kiem_tra: this.khoaInfo.cap_nhat_dot_kiem_tra,
      ngung_diem_danh: this.khoaInfo.ngung_diem_danh,
      // loai_tai_khoan: this.khoaInfo.loai_tai_khoan,
      // gioi_tinh: this.khoaInfo.gioi_tinh,
      // ngay_sinh: ngay(this.khoaInfo.ngay_sinh),
      // ngay_rua_toi: ngay(this.khoaInfo.ngay_rua_toi),
      // ngay_ruoc_le: ngay(this.khoaInfo.ngay_ruoc_le),
      // ngay_them_suc: ngay(this.khoaInfo.ngay_them_suc),
      // email: this.khoaInfo.email,
      // dien_thoai: this.khoaInfo.dien_thoai,
      // dia_chi: this.khoaInfo.dia_chi,
      // ghi_chu: this.khoaInfo.ghi_chu,
    });
  }

  save() {
    const _url = this.urlAPI + '/' + this.khoaInfo.id;
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
