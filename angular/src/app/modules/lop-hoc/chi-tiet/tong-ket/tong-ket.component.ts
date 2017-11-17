import { ToasterService } from 'angular2-toaster';
import { Component, OnInit, OnDestroy, AfterViewInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Store } from '@ngrx/store';

import { JwtAuthHttp } from './../../../../services/http-auth.service';
import { environment } from './../../../../../environments/environment';
import { AppState } from './../../../../store/reducers/index';

declare var jQuery: any;

@Component({
  selector: 'app-tong-ket',
  templateUrl: './tong-ket.component.html',
  styleUrls: ['./tong-ket.component.scss']
})
export class TongKetComponent implements AfterViewInit, OnDestroy {
  private lhAPI = environment.apiURL + '/lop-hoc';

  // tab = 'phieu-lien-lac'
  tab = 'thong-tin'

  isLoading = false;
  ckbGhiChu = false;

  pagingTN = {
    id: 'tnTable',
    itemsPerPage: 10,
    currentPage: 1,
  }

  lopHocID: number;
  curAuth: any;
  apiData: {
    Data: any[],
    DiemDanh: {},
    DiemSo: {},
    SoDot: number[],
    SoLan: number[],
  };

  sub$: any;
  subAuth$: any;

  constructor(
    private activeRoute: ActivatedRoute,
    private store: Store<AppState>,
    private toasterService: ToasterService,
    private _http: JwtAuthHttp,
  ) {

    this.sub$ = this.activeRoute.parent.params.subscribe(params => {
      this.lopHocID = params['id']; // Lấy ID Lớp từ URL
    });

    /**
     * Lấy thông tin khóa học
     * + Các ràng buộc hạn chế về về đợt, lần, điểm danh
     */
    this.subAuth$ = this.store.select((state: AppState) => state.auth).subscribe(res => {
      this.curAuth = res;
    });

    this.loadData();
  }

  ngAfterViewInit() {
  }

  ngOnDestroy() {
    this.sub$.unsubscribe();
    this.subAuth$.unsubscribe();
  }

  /**
   * Lấy dữ liệu từ server
   */
  loadData() {
    this.isLoading = true;
    this.apiData = null;
    this._http.get(this.lhAPI + '/' + this.lopHocID + '/tong-ket')
      .map(res => res.json()).subscribe(_res => {
        this.apiData = _res;
        this.isLoading = false;

        // Tooltips
        setTimeout(() => {
          jQuery('.tooltip-tongket').tooltip({
            selector: '[data-toggle=tooltip]',
            container: 'body'
          });
        }, 0);
      }, error => {
        this.toasterService.pop('error', 'Lỗi!', error);
        this.isLoading = false;
      })
  }

  /**
   * + Tài khoản được phân quyền 'danh-gia-cuoi-nam'
   */
  hasPermXepHang() {
    if (this.curAuth.phan_quyen.includes('danh-gia-cuoi-nam')) {
      return true;
    }
    return false;
  }

  /**
   * + Tài khoản được phân quyền 'nhan-xet'
   * hoặc
   * + Đang dạy chính lớp này
   */
  hasPermNhanXet() {
    if (this.curAuth.phan_quyen.includes('nhan-xet') ||
      this.curAuth.lop_hoc_hien_tai_id.toString() === this.lopHocID.toString()) {
      return true;
    }
    return false;
  }

  update(_info) {
    this.tab = 'thong-tin';
    if (_info) {
      this.loadData();
    }
  }

  tbCaNam(_tn) {
    return Math.round(_tn.pivot.tb_canam * 100) / 100;
  }
}
