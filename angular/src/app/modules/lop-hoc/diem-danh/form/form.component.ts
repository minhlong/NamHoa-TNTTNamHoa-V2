import { ActivatedRoute } from '@angular/router';
import { Store } from '@ngrx/store';
import { Component, OnInit, Input, Output, EventEmitter, OnDestroy } from '@angular/core';
import { ToasterService } from 'angular2-toaster';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

import { JwtAuthHttp } from './../../../../services/http-auth.service';
import { environment } from './../../../../../environments/environment';
import { AppState } from './../../../../store/reducers/index';

@Component({
  selector: 'app-form-diem-danh',
  templateUrl: './form.component.html',
  styleUrls: ['./form.component.scss']
})
export class FormDiemDanhComponent implements OnInit, OnDestroy {
  @Input() apiData;
  @Input() thieuNhiArr;
  @Output() updateInfo = new EventEmitter();

  private urlAPI = environment.apiURL + '/lop-hoc';

  pagingTN = {
    id: 'tnTable',
    itemsPerPage: 10,
    currentPage: 1,
  }

  lopHocID: null;
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
      ngay: this.apiData.sunday,
      thieu_nhi: this._fb.array(this.initChuyenCan()),
    });
  }

  ngOnDestroy() {
    this.sub$.unsubscribe();
  }

  private initChuyenCan() {
    const tmpArr = [];
    this.thieuNhiArr.forEach(_tn => {
      const tmpTn = this.findChuyenCan(_tn);
      tmpArr.push(this._fb.group({
        id: _tn.id,
        ho_va_ten: _tn.ho_va_ten,
        di_le: tmpTn.di_le,
        di_hoc: tmpTn.di_hoc,
        ghi_chu: tmpTn.ghi_chu,
      }));
    });

    return tmpArr;
  }

  private findChuyenCan(tn) {
    let res;
    if (this.apiData) {
      res = this.apiData.data.find(c => c.tai_khoan_id === tn.id);
    }
    return res ? res : {};
  }

  save() {
    const _url = this.urlAPI + '/' + this.lopHocID + '/chuyen-can';
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
