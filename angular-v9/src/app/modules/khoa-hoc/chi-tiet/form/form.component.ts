import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { ToasterService } from 'angular2-toaster';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';

import { HttpClient } from '@angular/common/http'; // import { JwtAuthHttp } from '../../../../services/http-auth.service';
import { environment } from 'src/environments/environment';
import { ngay } from '../../../shared/utities.pipe';

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
  dotKTArr: any = [];
  maskOption = {
    mask: [/[0-9]/, /[0-9]/, '-', /[0-9]/, /[0-9]/, '-', /[0-9]/, /[0-9]/, /[0-9]/, /[0-9]/],
    guide: true,
  }

  constructor(
    private toasterService: ToasterService,
    private formBuilder: FormBuilder,
    private _http: HttpClient,
  ) {
  }

  ngOnInit() {
    this.dotKTArr = Array(this.khoaInfo.so_dot_kiem_tra).fill(null).map((x, i) => i + 1);

    this.infoFB = this.formBuilder.group({
      id: this.khoaInfo.id,
      ngay_bat_dau: ngay(this.khoaInfo.ngay_bat_dau),
      ngay_ket_thuc: ngay(this.khoaInfo.ngay_ket_thuc),
      so_dot_kiem_tra: this.khoaInfo.so_dot_kiem_tra,
      so_lan_kiem_tra: this.khoaInfo.so_lan_kiem_tra,
      cap_nhat_dot_kiem_tra: this.khoaInfo.cap_nhat_dot_kiem_tra,
      ngung_diem_danh: this.khoaInfo.ngung_diem_danh,
    });

    // Tạo form Đi Lễ
    let group: any = {};
    Object.keys(this.khoaInfo.di_le).forEach(_key => {
      group[_key] = new FormControl(this.khoaInfo.di_le[_key] || null);
    });
    this.infoFB.addControl('di_le', new FormGroup(group));

    // Tạo form Đi Học
    group = {};
    Object.keys(this.khoaInfo.di_hoc).forEach(_key => {
      group[_key] = new FormControl(this.khoaInfo.di_hoc[_key] || null);
    });
    this.infoFB.addControl('di_hoc', new FormGroup(group));

    // Tạo form Xếp Loại
    group = {};
    let subGroup = {};
    let subKey = 'CHUYEN_CAN';
    subGroup['TB'] = new FormControl(this.khoaInfo.xep_loai[subKey]['TB'] || null);
    subGroup['KHA'] = new FormControl(this.khoaInfo.xep_loai[subKey]['KHA'] || null);
    subGroup['GIOI'] = new FormControl(this.khoaInfo.xep_loai[subKey]['GIOI'] || null);
    group[subKey] = new FormGroup(subGroup);

    subGroup = {};
    subKey = 'HOC_LUC';
    subGroup['TB'] = new FormControl(this.khoaInfo.xep_loai[subKey]['TB'] || null);
    subGroup['KHA'] = new FormControl(this.khoaInfo.xep_loai[subKey]['KHA'] || null);
    subGroup['GIOI'] = new FormControl(this.khoaInfo.xep_loai[subKey]['GIOI'] || null);
    group[subKey] = new FormGroup(subGroup);

    this.infoFB.addControl('xep_loai', new FormGroup(group));

    // Tạo form Xếp Hạng
    group = {};
    subGroup = {};
    subKey = 'CHUYEN_CAN';
    subGroup['LEN_LOP'] = new FormControl(this.khoaInfo.xep_hang[subKey]['LEN_LOP'] || null);
    subGroup['KHUYEN_KHICH'] = new FormControl(this.khoaInfo.xep_hang[subKey]['KHUYEN_KHICH'] || null);
    subGroup['III'] = new FormControl(this.khoaInfo.xep_hang[subKey]['III'] || null);
    subGroup['II'] = new FormControl(this.khoaInfo.xep_hang[subKey]['II'] || null);
    subGroup['I'] = new FormControl(this.khoaInfo.xep_hang[subKey]['I'] || null);
    group[subKey] = new FormGroup(subGroup);

    subGroup = {};
    subKey = 'HOC_LUC';
    subGroup['LEN_LOP'] = new FormControl(this.khoaInfo.xep_hang[subKey]['LEN_LOP'] || null);
    subGroup['KHUYEN_KHICH'] = new FormControl(this.khoaInfo.xep_hang[subKey]['KHUYEN_KHICH'] || null);
    subGroup['III'] = new FormControl(this.khoaInfo.xep_hang[subKey]['III'] || null);
    subGroup['II'] = new FormControl(this.khoaInfo.xep_hang[subKey]['II'] || null);
    subGroup['I'] = new FormControl(this.khoaInfo.xep_hang[subKey]['I'] || null);
    group[subKey] = new FormGroup(subGroup);

    subGroup = {};
    subKey = 'SO_LUONG';
    subGroup['KHUYEN_KHICH'] = new FormControl(this.khoaInfo.xep_hang[subKey]['KHUYEN_KHICH'] || null);
    subGroup['III'] = new FormControl(this.khoaInfo.xep_hang[subKey]['III'] || null);
    subGroup['II'] = new FormControl(this.khoaInfo.xep_hang[subKey]['II'] || null);
    subGroup['I'] = new FormControl(this.khoaInfo.xep_hang[subKey]['I'] || null);
    group[subKey] = new FormGroup(subGroup);

    this.infoFB.addControl('xep_hang', new FormGroup(group));
  }

  save() {
    const _url = this.urlAPI + '/' + this.khoaInfo.id;
    const _par = Object.assign({}, this.infoFB.value, {
      ngay_bat_dau: ngay(this.infoFB.value.ngay_bat_dau),
      ngay_ket_thuc: ngay(this.infoFB.value.ngay_ket_thuc),
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

  /**
   * Chỉ cho nhập số
   */
  _keyPress(event: any) {
    const pattern = /[0-9\.]/;
    const inputChar = String.fromCharCode(event.charCode);

    if (!pattern.test(inputChar)) {
      // invalid character, prevent input
      event.preventDefault();
    }
  }
}
