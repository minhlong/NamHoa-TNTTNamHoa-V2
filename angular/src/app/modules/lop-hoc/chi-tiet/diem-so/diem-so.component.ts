import { ToasterService } from 'angular2-toaster';
import { Component, OnInit, OnDestroy } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Store } from '@ngrx/store';
import { URLSearchParams } from '@angular/http';

import { JwtAuthHttp } from '../../../../services/http-auth.service';
import { environment } from 'environments/environment';
import { AppState } from '../../../../store/reducers';

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

  lopHocID: number;
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
      this.lopHocID = params['id']; // Lấy ID Lớp từ URL
    });

    /**
     * Lấy thông tin khóa học
     * + Các ràng buộc hạn chế về về đợt, lần, điểm danh
     */
    this.subAuth$ = this.store.select((state: AppState) => state.auth).subscribe(res => {
      this.curAuth = res;
      this.dotKTHienTai = this.dotKTKhoaHoc = res.khoa_hoc_hien_tai.cap_nhat_dot_kiem_tra;
      if (this.dotKTHienTai <= 0) {
        this.dotKTHienTai = 1;
      }
      this.dotKTArr = Array(res.khoa_hoc_hien_tai.so_dot_kiem_tra).fill(null).map((x, i) => i + 1);
      this.lanKTArr = Array(res.khoa_hoc_hien_tai.so_lan_kiem_tra).fill(null).map((x, i) => i + 1);
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
   * Lấy dữ liệu điêm từ server
   */
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
   * + Chỉ được điểm danh lớp của mình
   *   và Đợt kiểm tra hiện tại trong khóa học phải được mở
   */
  hasPerm() {
    if (this.curAuth.phan_quyen.includes('diem-danh')) {
      return true;
    }

    if (this.curAuth.lop_hoc_hien_tai_id.toString() === this.lopHocID.toString() &&
      this.dotKTHienTai &&
      this.dotKTKhoaHoc &&
      this.dotKTHienTai.toString() === this.dotKTKhoaHoc.toString()) {
      return true;
    }

    return false;
  }

  /**
   * Mở form để chỉnh sửa
   * @param _lan Lần kiểm tra thứ x
   */
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

  /**
   * Tìm dữ liệu điểm của học viên ở lần kiểm tra x
   * @param _tn Học viên
   * @param _lan Lần kiểm tra
   */
  findDiemSo(_tn, _lan) {
    let res;
    if (this.apiData) {
      res = this.apiData.data.find(c => c.tai_khoan_id === _tn.id && c.lan === _lan);
      if (res) {
        return res;
      }
    }
    return res ? res : {};
  }
}
