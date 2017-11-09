import { ToasterService } from 'angular2-toaster';
import { Component, OnInit, OnDestroy } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Store } from '@ngrx/store';
import { URLSearchParams } from '@angular/http';

import { JwtAuthHttp } from '../../../services/http-auth.service';
import { environment } from './../../../../environments/environment';
import { AppState } from './../../../store/reducers/index';
import { GetLopInfoSucc } from '../../../store/actions/lop-hoc.action';
import { ngay } from '../../shared/convert-type.pipe';

@Component({
  selector: 'app-diem-so',
  templateUrl: './diem-so.component.html',
  styleUrls: ['./diem-so.component.scss']
})
export class DiemSoComponent implements OnDestroy {
  private lhAPI = environment.apiURL + '/lop-hoc';

  tab = 'thong-tin'
  isLoading = false;

  pagingTN = {
    id: 'tnTable',
    itemsPerPage: 10,
    currentPage: 1,
  }

  lopHocID: null;
  curAuth: any;
  lanKTHienTai: number;
  dotKTHienTai: number;
  dotKTKhoaHoc: number;
  dotKTArr: any = [];
  lanKTArr: any = [];
  thieuNhiArr = [];
  apiData: {
    data: any[],
    sunday: null,
  };

  sub$: any;
  subAuth$: any;
  subTn$: any;

  constructor(
    private activeRoute: ActivatedRoute,
    private store: Store<AppState>,
    private toasterService: ToasterService,
    private _http: JwtAuthHttp,
  ) {

    this.sub$ = this.activeRoute.parent.params.subscribe(params => {
      this.lopHocID = params['id'];
    });
    this.subAuth$ = this.store.select((state: AppState) => state.auth).subscribe(res => {
      this.curAuth = res;
      this.dotKTHienTai = this.dotKTKhoaHoc = res.khoa_hoc_hien_tai.cap_nhat_dot_kiem_tra;
      this.dotKTArr = Array(res.khoa_hoc_hien_tai.so_dot_kiem_tra).fill(null).map((x, i) => i + 1);
      this.lanKTArr = Array(res.khoa_hoc_hien_tai.so_lan_kiem_tra).fill(null).map((x, i) => i + 1);
    });
    this.subTn$ = this.store.select((state: AppState) => state.lop_hoc.thieu_nhi).subscribe(res => {
      this.thieuNhiArr = res;
      if (this.thieuNhiArr.length) {
        this.loadData();
      }
    });
  }

  ngOnDestroy() {
    this.sub$.unsubscribe();
    this.subAuth$.unsubscribe();
    this.subTn$.unsubscribe();
  }

  loadData() {
    if (!this.dotKTHienTai) {
      return;
    }
    const search = new URLSearchParams();
    search.set('dot', this.dotKTHienTai.toString());

    this.isLoading = true;
    this.apiData = null;
    this._http.get(this.lhAPI + '/' + this.lopHocID + '/hoc-luc', { search })
      .map(res => res.json()).subscribe(_res => {
        this.apiData = _res;
        console.log(this.apiData);
        this.isLoading = false;
      }, error => {
        this.toasterService.pop('error', 'Lỗi!', error);
        this.isLoading = false;
      })
  }

  /**
   * Kiểm tra quyền vào điểm
   * + Tài khoản được phân quyền 'diem-danh'
   * hoặc
   * + Đợt kiểm tra hiện tại trong khóa học phải được mở
   */
  hasPerm() {
    if (this.curAuth.phan_quyen.includes('diem-danh')) {
      return true;
    }

    if (this.dotKTHienTai &&
      this.dotKTKhoaHoc &&
      this.dotKTHienTai.toString() === this.dotKTKhoaHoc.toString()) {
      return true;
    }

    return false;
  }

  openForm(_lan) {
    this.lanKTHienTai = _lan;
    this.tab = 'form';
  }

  update(_info) {
    this.tab = 'thong-tin';
    if (_info) {
      this.loadData();
    }
  }
}
