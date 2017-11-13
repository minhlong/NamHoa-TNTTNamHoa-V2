import { ActivatedRoute } from '@angular/router';
import { Store } from '@ngrx/store';
import { Component, OnInit, Input, Output, EventEmitter, OnDestroy } from '@angular/core';
import { ToasterService } from 'angular2-toaster';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

import { JwtAuthHttp } from './../../../../services/http-auth.service';
import { environment } from './../../../../../environments/environment';
import { AppState } from './../../../../store/reducers/index';

@Component({
  selector: 'app-form-nhan-xet',
  templateUrl: './form-nhan-xet.component.html',
  styleUrls: ['./form-nhan-xet.component.scss']
})
export class FormNhanXetComponent implements OnInit, OnDestroy {
  @Input() apiData;
  @Output() updateInfo = new EventEmitter();

  private urlAPI = environment.apiURL + '/lop-hoc';

  pagingTN = {
    id: 'tnTable',
    itemsPerPage: 10,
    currentPage: 1,
  }

  lopHocID: number;
  isLoading: boolean;
  formGroup: FormGroup;
  error: any;
  sub$: any;

  constructor(
    private activeRoute: ActivatedRoute,
    private store: Store<AppState>,
    private toasterService: ToasterService,
    private _fb: FormBuilder,
    private _http: JwtAuthHttp,
  ) {
    this.sub$ = this.activeRoute.parent.params.subscribe(params => {
      this.lopHocID = params['id'];
    });
  }

  ngOnInit() {
    this.formGroup = this._fb.group({
      thieu_nhi: this._fb.array(this.initXepHang()),
    });
  }

  ngOnDestroy() {
    this.sub$.unsubscribe();
  }

  private initXepHang() {
    const tmpArr = [];
    this.apiData.Data.forEach(_tn => {
      tmpArr.push(this._fb.group({
        id: _tn.id,
        ho_va_ten: _tn.ho_va_ten,
        nhan_xet: _tn.pivot.nhan_xet,
      }));
    });

    return tmpArr;
  }

  save() {
    const _url = this.urlAPI + '/' + this.lopHocID + '/tong-ket/nhan-xet';
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
