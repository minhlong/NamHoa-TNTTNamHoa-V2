import { ToasterService } from 'angular2-toaster';
import { Component, OnInit, OnDestroy } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Store } from '@ngrx/store';

import { JwtAuthHttp } from '../../../services/http-auth.service';
import { environment } from './../../../../environments/environment';
import { AppState } from './../../../store/reducers/index';

@Component({
  selector: 'app-tong-ket',
  templateUrl: './tong-ket.component.html',
  styleUrls: ['./tong-ket.component.scss']
})
export class TongKetComponent implements OnDestroy {
  private lhAPI = environment.apiURL + '/lop-hoc';

  tab = 'phieu-lien-lac'
  isLoading = false;
  ckbGhiChu = false;

  pagingTN = {
    id: 'tnTable',
    itemsPerPage: 10,
    currentPage: 1,
  }

  lopHocID: number;
  curAuth: any;
  thieuNhiArr = [];
  apiData: {
    Data: any[],
    DiemDanh: {},
    DiemSo: {},
    SoDot: number[],
    SoLan: number[],
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
      this.lopHocID = params['id']; // Lấy ID Lớp từ URL
    });

    /**
     * Lấy thông tin khóa học
     * + Các ràng buộc hạn chế về về đợt, lần, điểm danh
     */
    this.subAuth$ = this.store.select((state: AppState) => state.auth).subscribe(res => {
      this.curAuth = res;
    });

    /**
     * Lấy thông tin thiếu nhi từ lớp học
     */
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
      }, error => {
        this.toasterService.pop('error', 'Lỗi!', error);
        this.isLoading = false;
      })
  }

  /**
   * Tìm đữ liệu cho học viên
   * @param tn Học Viên
   */
  findTongKet(tn) {
    let res;
    if (this.apiData) {
      res = this.apiData.Data.find(c => c.id === tn.id);
      if (res) {
        res.pivot.trung_binh_cong = (res.pivot.hoc_luc + res.pivot.chuyen_can) / 2;
        res.pivot.trung_binh_cong = Math.round(res.pivot.trung_binh_cong * 1000) / 1000;
        return res.pivot;
      }
    }
    return res ? res : {};
  }

  /**
   * Kiểm tra quyền điểm danh
   * + Tài khoản được phân quyền 'danh-gia-cuoi-nam'
   */
  hasPermXepHang() {
    if (this.curAuth.phan_quyen.includes('danh-gia-cuoi-nam') ||
      this.curAuth.lop_hoc_hien_tai_id.toString() === this.lopHocID.toString()) {

      return true;
    }
    return false;
  }

  /**
   * Kiểm tra quyền điểm danh
   * + Tài khoản được phân quyền 'nhan-xet'
   * hoặc
   * + Đang dạy chính lớp này
   */
  hasPermNhanXet() {
    if (this.curAuth.phan_quyen.includes('nhan-xet')) {
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
}
