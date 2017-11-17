import { ActivatedRoute } from '@angular/router';
import { Store } from '@ngrx/store';
import { Component, OnInit, Input, Output, EventEmitter, OnDestroy } from '@angular/core';
import { ToasterService } from 'angular2-toaster';
import { FormBuilder, FormGroup, Validators, FormArray } from '@angular/forms';
import { Subject } from 'rxjs/Rx';
import { URLSearchParams } from '@angular/http';

import { JwtAuthHttp } from 'app/services/http-auth.service';
import { AppState } from 'app/store/reducers';
import { environment } from 'environments/environment';
import { bodauTiengViet } from '../../../../_helpers';
import { ngay } from '../../../shared/convert-type.pipe';

@Component({
  selector: 'app-form-tao-moi',
  templateUrl: './form-tao-moi.component.html',
  styleUrls: ['./form-tao-moi.component.scss']
})
export class FormTaoMoiComponent implements OnInit {
  @Output() updateInfo = new EventEmitter();

  private urlAPI = environment.apiURL + '/thu-moi';
  maskOption = {
    mask: [/[0-9]/, /[0-9]/, '-', /[0-9]/, /[0-9]/, '-', /[0-9]/, /[0-9]/, /[0-9]/, /[0-9]/],
    guide: true,
  }

  arrayOfStrings: string[] = ['this', 'is', 'array', 'of', 'text'];
  model1: any;

  isLoading = false;

  formGroup: FormGroup;
  error: any;

  constructor(
    private store: Store<AppState>,
    private toasterService: ToasterService,
    private _fb: FormBuilder,
    private _http: JwtAuthHttp,
  ) {
  }

  ngOnInit() {
    // Tạo form để submit lên server
    this.formGroup = this._fb.group({
      ngay: null,
      tai_khoan_id: null,
      ghi_chu: null,
    });
  }

  /**
   *    Nếu OK -> quay lại trang chính
   *    Nếu lỗi -> hiện lỗi
   */
  save() {
    this.isLoading = true;
    const _par = Object.assign({}, this.formGroup.value, {
      ngay: ngay(this.formGroup.value.ngay),
    });

    this._http.post(this.urlAPI, _par).map(res => res.json()).subscribe(res => {
      this.isLoading = false;
      this.updateInfo.emit(true);
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

  /**
   * Thoát -> quay lại trang chính
   */
  cancel() {
    this.updateInfo.emit(false);
  }
}
