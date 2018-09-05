import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { ToasterService } from 'angular2-toaster';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

import { JwtAuthHttp } from '../../../../services/http-auth.service';
import { environment } from 'environments/environment';

@Component({
  selector: 'app-mat-khau',
  templateUrl: './mat-khau.component.html',
  styleUrls: ['./mat-khau.component.scss']
})
export class MatKhauComponent implements OnInit {
  private urlAPI = environment.apiURL + '/tai-khoan';

  @Input() taiKhoanInfo: any;
  @Output() updateInfo = new EventEmitter();

  isLoading: boolean;
  infoFB: FormGroup;

  constructor(
    private toasterService: ToasterService,
    private formBuilder: FormBuilder,
    private _http: JwtAuthHttp,
  ) { }

  ngOnInit() {
    this.infoFB = this.formBuilder.group({
      mat_khau: null,
      mat_khau_confirm: null,
    });
  }

  save() {
    const _url = this.urlAPI + '/' + this.taiKhoanInfo.id + '/mat-khau';

    this.isLoading = true;
    this._http.post(_url, this.infoFB.value).map(res => res.json()).subscribe(res => {
      this.isLoading = false;
      this.updateInfo.emit(null);
      this.toasterService.pop('success', 'Mật Khẩu!', 'Đã cập nhật');
    }, _err => {
      this.isLoading = false;
      this.toasterService.pop('error', 'Lỗi!', _err);
    });
  }

  cancel() {
    this.updateInfo.emit(null);
  }

}
