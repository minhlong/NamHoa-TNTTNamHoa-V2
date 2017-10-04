import { Store } from '@ngrx/store';
import { Component, OnInit, Input, Output, EventEmitter, OnDestroy } from '@angular/core';
import { ToasterService } from 'angular2-toaster';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { URLSearchParams } from '@angular/http';
import { Subject } from 'rxjs/Rx';

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
  private taiKhoanSrcArr = [];

  @Output() updateInfo = new EventEmitter();

  isLoading: boolean;
  error: any;
  sub: any;
  htSub: any;
  lhSub: any;
  lopHocInfo: any = {};
  huynhTruongArr = [];
  taiKhoanArr = [];
  search$ = new Subject<any>();

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
      search.set('chua_xep_lop', _khoa.id);
      search.set('trang_thai', 'HOAT_DONG');
      search.set('loai_tai_khoan', 'HUYNH_TRUONG');
      this._http.get(this.tkAPI, { search }).map(res => res.json()).subscribe(res => {
        this.taiKhoanSrcArr = res.data;
        this.search$.next(''); // Trigger Search
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

    this.search$.debounceTime(400).subscribe((_str) => {
      this.taiKhoanArr = this.taiKhoanSrcArr.filter(el => {
        return el.ho_va_ten.toLowerCase().indexOf(_str.toLowerCase()) !== -1
      });
    });
  }

  ngOnInit() {
  }

  ngOnDestroy() {
    this.search$.complete();
    this.sub.unsubscribe();
    this.lhSub.unsubscribe();
    this.htSub.unsubscribe();
  }

  checkAll(_arr: any[], _event) {
    _arr.forEach(_el => {
      _el.checked = _event.target.checked
    });
  }

  getChecked(_arr: any[]) {
    return _arr.filter((_el) => {
      return _el.checked === true;
    }).length;
  }

  cancel() {
    this.updateInfo.emit(null);
  }

}
