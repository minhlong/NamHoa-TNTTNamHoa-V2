import { Store } from '@ngrx/store';
import { Router } from '@angular/router';
import { ToasterService } from 'angular2-toaster';
import { Component, OnInit, OnDestroy } from '@angular/core';

import { JwtAuthHttp } from '../../services/http-auth.service';
import { environment } from 'environments/environment';
import { defaultPageState } from '../../modules/tai-khoan/danh-sach/defaultPageState';
import { AppState } from '../../store/reducers';

@Component({
  selector: 'app-trang-chu',
  templateUrl: './trang-chu.component.html',
  styleUrls: ['./trang-chu.component.scss']
})
export class TrangChuComponent implements OnDestroy {
  sub$: any;
  khoaHienTai: any;
  urlAPI = environment.apiURL + '/trang-chu';
  isLoading = true;
  itemSelected = null;
  chuaDiemDanh: any;
  siSo: any;
  pState = {
    // Paging
    id: 'TrangChu-List-Page',
    itemsPerPage: 4,
    currentPage: 1,
  }

  constructor(
    private store: Store<AppState>,
    private router: Router,
    private toasterService: ToasterService,
    private _http: JwtAuthHttp,
  ) {
    this.resetData();
    this.loadData();

    // Lấy thông số cơ cấu điểm và ràng buộc từ thông tin khóa học
    this.sub$ = this.store.select((state: AppState) => state.auth.khoa_hoc_hien_tai).subscribe(res => {
      this.khoaHienTai = res;
    });
  }

  private resetData() {
    this.siSo = {
      au_nhi: 0,
      ht_du_bi: 0,
      huynh_truong: 0,
      nghia_si: 0,
      thieu_nhi: 0,
    };
    this.chuaDiemDanh = {
      lop: [],
      ngay: null,
    };
  }

  loadData() {
    this.isLoading = true;

    this._http.get(this.urlAPI).map(res => res.json()).subscribe(res => {
      this.siSo = res.si_so;
      this.isLoading = false;
      this.chuaDiemDanh = res.chua_diem_danh;
    }, error => {
      this.resetData();
      this.isLoading = false;
      this.toasterService.pop('error', 'Lỗi!', error);
    })
  }

  xemTaiKhoan(taiKhoan) {
    this.router.navigate(['/tai-khoan/chi-tiet/', taiKhoan.id]);
  }

  xemNganh(_nganh, _loaiTK) {
    localStorage.setItem(defaultPageState.id, JSON.stringify({
      Floai_tai_khoan: _loaiTK,
      Fnganh: _nganh,
    }));

    this.router.navigate(['/tai-khoan']);
  }

  ngOnDestroy() {
    this.sub$.unsubscribe();
  }
}
