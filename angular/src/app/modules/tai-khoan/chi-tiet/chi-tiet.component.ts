import { ToasterService } from 'angular2-toaster';
import { Component, OnInit, OnDestroy } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Store } from '@ngrx/store';

import { JwtAuthHttp } from '../../../services/http-auth.service';
import { environment } from './../../../../environments/environment';
import { AppState } from './../../../store/reducers/index';

@Component({
  selector: 'app-chi-tiet',
  templateUrl: './chi-tiet.component.html',
  styleUrls: ['./chi-tiet.component.scss']
})
export class ChiTietComponent implements OnInit, OnDestroy {
  urlAPI = environment.apiURL + '/tai-khoan';
  tab = 'chi-tiet'; // form, mat-khau
  itemSelected = null;
  isLoading: boolean;

  pState = {
    // Paging
    id: 'TaiKhoan-ChiTiet-Page',
    itemsPerPage: 3,
    currentPage: 1,
  }

  taiKhoanID: string;
  taiKhoanInfo: any = {};

  parSub: any;
  authSub: any;
  curAuth: any;

  constructor(
    private store: Store<AppState>,
    private router: Router,
    private toasterService: ToasterService,
    private _http: JwtAuthHttp,
    private activatedRoute: ActivatedRoute
  ) {
    this.parSub = this.activatedRoute.params.subscribe(params => {
      this.taiKhoanID = params['id'];

      this._http.get(this.urlAPI + '/' + this.taiKhoanID)
        .map(res => res.json()).subscribe(res => {
          this.isLoading = false;
          this.taiKhoanInfo = res;
        }, error => {
          this.isLoading = false;
          this.toasterService.pop('error', 'Lỗi!', error);
        });
    })

    this.authSub = this.store.select((state: AppState) => state.auth).subscribe(res => {
      this.curAuth = res;
    });
  }

  ngOnInit() {
  }

  hasPerm() {
    if (this.curAuth.phan_quyen.includes('tai-khoan') || this.taiKhoanID.toUpperCase() === this.curAuth.tai_khoan.id) {
      return true;
    }
    return false;
  }

  xemTaiKhoan(taiKhoan) {
    this.router.navigate(['/tai-khoan/chi-tiet/', taiKhoan.id]);
  }

  update(_info) {
    this.tab = 'chi-tiet';

    if (!_info) {
      return; // Click Thoát
    }
    this.taiKhoanInfo = _info;
  }

  ngOnDestroy() {
    this.parSub.unsubscribe();
    this.authSub.unsubscribe();
  }
}
