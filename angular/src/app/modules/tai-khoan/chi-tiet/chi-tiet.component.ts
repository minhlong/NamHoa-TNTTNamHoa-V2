import { ToasterService } from 'angular2-toaster';
import { Component, OnInit, OnDestroy } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { JwtAuthHttp } from '../../../services/http-auth.service';
import { environment } from './../../../../environments/environment';

@Component({
  selector: 'app-chi-tiet',
  templateUrl: './chi-tiet.component.html',
  styleUrls: ['./chi-tiet.component.scss']
})
export class ChiTietComponent implements OnInit, OnDestroy {
  itemSelected = null;
  isLoading: boolean;
  taiKhoanID: any;
  taiKhoanInfo: any = {};
  parSub: any;
  webAPI = environment.webURL + '/tai-khoan';
  urlAPI = environment.apiURL + '/tai-khoan';
  pState = {
    // Paging
    id: 'TaiKhoan-ChiTiet-Page',
    itemsPerPage: 3,
    currentPage: 1,
  }

  constructor(
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
  }

  xemTaiKhoan(taiKhoan) {
    this.router.navigate(['/tai-khoan/chi-tiet/', taiKhoan.id]);
  }

  ngOnInit() {
  }

  ngOnDestroy() {
    this.parSub.unsubscribe();
  }
}
