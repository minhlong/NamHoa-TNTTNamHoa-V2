import { Store } from '@ngrx/store';
import { Component, OnInit, Input, Output, EventEmitter, OnDestroy } from '@angular/core';
import { ToasterService } from 'angular2-toaster';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { URLSearchParams } from '@angular/http';

import { JwtAuthHttp } from './../../../../services/http-auth.service';
import { environment } from './../../../../../environments/environment';
import { AppState } from './../../../../store/reducers/index';

@Component({
  selector: 'app-huynh-truong',
  templateUrl: './huynh-truong.component.html',
  styleUrls: ['./huynh-truong.component.scss']
})
export class HuynhTruongComponent implements OnInit, OnDestroy {
  private tkAPI = environment.apiURL + '/tai-khoan';
  private lhAPI = environment.apiURL + '/lop-hoc';
  @Output() updateInfo = new EventEmitter();

  isLoading: boolean;
  infoFB: FormGroup;
  error: any;
  sub: any;
  htSub: any;
  lhSub: any;
  lopHocInfo: any = {};
  huynhTruongArr = [];
  taiKhoanArr = [];

  pagingHT = {
    id: 'htTable',
    itemsPerPage: 10,
    currentPage: 1,
  }

  constructor(
    private store: Store<AppState>,
    private toasterService: ToasterService,
    private formBuilder: FormBuilder,
    private _http: JwtAuthHttp,
  ) {

    this.sub = this.store.select((state: AppState) => state.auth.khoa_hoc_hien_tai).subscribe(_khoa => {
      const search = new URLSearchParams();
      search.set('khoa', _khoa.id);
      search.set('trang_thai', 'HOAT_DONG');
      search.set('loai_tai_khoan', 'HUYNH_TRUONG');
      this._http.get(this.tkAPI, { search }).map(res => res.json()).subscribe(res => {
        this.taiKhoanArr = res.data;
      }, error => {
        this.toasterService.pop('error', 'Lỗi!', error);
      })
    });

    this.lhSub = this.store.select((state: AppState) => state.lop_hoc.thong_tin).subscribe(res => {
      this.lopHocInfo = res;
    });

    this.htSub = this.store.select((state: AppState) => state.lop_hoc.huynh_truong).subscribe(res => {
      this.huynhTruongArr = res;
    });
  }

  ngOnInit() {
    this.infoFB = this.formBuilder.group({
    });
  }

  ngOnDestroy() {
    this.sub.unsubscribe();
    this.lhSub.unsubscribe();
    this.htSub.unsubscribe();
  }

  save() {
    const _url = this.lhAPI + '/' + this.lopHocInfo.id;
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
