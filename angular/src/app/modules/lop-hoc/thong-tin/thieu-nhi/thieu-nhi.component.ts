import { Store } from '@ngrx/store';
import { Component, OnInit, Input, Output, EventEmitter, OnDestroy } from '@angular/core';
import { ToasterService } from 'angular2-toaster';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

import { JwtAuthHttp } from './../../../../services/http-auth.service';
import { environment } from './../../../../../environments/environment';
import { AppState } from './../../../../store/reducers/index';

@Component({
  selector: 'app-thieu-nhi',
  templateUrl: './thieu-nhi.component.html',
  styleUrls: ['./thieu-nhi.component.scss']
})
export class ThieuNhiComponent implements OnDestroy {
  private urlAPI = environment.apiURL + '/lop-hoc';
  @Output() updateInfo = new EventEmitter();

  isLoading: boolean;
  infoFB: FormGroup;
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
    this.infoFB = this.formBuilder.group({
    });
  }

  ngOnDestroy() {
    this.lhSub.unsubscribe();
  }

  save() {
    const _url = this.urlAPI + '/' + this.lopHocInfo.id;
    this.isLoading = true;

    this._http.post(_url, this.infoFB.value).map(res => res.json()).subscribe(res => {
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
