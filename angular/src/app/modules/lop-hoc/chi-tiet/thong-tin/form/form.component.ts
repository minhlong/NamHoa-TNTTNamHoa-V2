import { Store } from '@ngrx/store';
import { Component, OnInit, Input, Output, EventEmitter, OnDestroy } from '@angular/core';
import { ToasterService } from 'angular2-toaster';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

import { JwtAuthHttp } from '../../../../../services/http-auth.service';
import { environment } from 'environments/environment';
import { AppState } from '../../../../../store/reducers';

@Component({
  selector: 'app-form-thong-tin',
  templateUrl: './form.component.html',
  styleUrls: ['./form.component.scss']
})
export class FormThongTinComponent implements OnInit, OnDestroy {
  private urlAPI = environment.apiURL + '/lop-hoc';
  @Output() updateInfo = new EventEmitter();

  isLoading: boolean;
  formGroup: FormGroup;
  error: any;
  lhSub: any;
  lopHocInfo: any = {};

  constructor(
    private store: Store<AppState>,
    private toasterService: ToasterService,
    private formBuilder: FormBuilder,
    private _http: JwtAuthHttp,
  ) {
    this.lhSub = this.store.select((state: AppState) => state.lop_hoc.thong_tin).subscribe(res => {
      this.lopHocInfo = res;
    });
  }

  ngOnInit() {
    this.formGroup = this.formBuilder.group({
      nganh: this.lopHocInfo.nganh,
      cap: this.lopHocInfo.cap,
      doi: this.lopHocInfo.doi,
      vi_tri_hoc: this.lopHocInfo.vi_tri_hoc,
    });
  }

  ngOnDestroy() {
    this.lhSub.unsubscribe();
  }

  save() {
    const _url = this.urlAPI + '/' + this.lopHocInfo.id;
    this.isLoading = true;

    this._http.post(_url, this.formGroup.value).map(res => res.json()).subscribe(res => {
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
