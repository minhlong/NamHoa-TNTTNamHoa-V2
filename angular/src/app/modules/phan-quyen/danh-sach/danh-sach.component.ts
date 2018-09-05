import { Store } from '@ngrx/store';
import { ToasterService } from 'angular2-toaster';
import { Component, OnDestroy } from '@angular/core';

import { JwtAuthHttp } from '../../../services/http-auth.service';
import { environment } from 'environments/environment';
import { AuthState } from '../../../store/reducers/auth.reducer';
import { AppState } from '../../../store/reducers';

@Component({
  selector: 'app-danh-sach',
  templateUrl: './danh-sach.component.html',
  styleUrls: ['./danh-sach.component.scss']
})
export class DanhSachComponent implements OnDestroy {
  urlAPI = environment.apiURL + '/phan-quyen';

  tab = 'danh-sach';
  dataArr = [];
  isLoading = false;
  curAuth: AuthState;
  itemSelected;

  paging = {
    // Paging
    id: 'phan-quyen-List-Page',
    itemsPerPage: 10,
    currentPage: 1,
  }

  subAuth$: any;

  constructor(
    private toasterService: ToasterService,
    private store: Store<AppState>,
    private _http: JwtAuthHttp,
  ) {
    this.subAuth$ = this.store.select((state: AppState) => state.auth).subscribe(res => {
      this.curAuth = res;
    });

    this.loadData();
  }

  ngOnDestroy() {
    this.subAuth$.unsubscribe()
  }

  private loadData() {
    this.isLoading = true;
    this._http.get(this.urlAPI).map(res => res.json()).subscribe(res => {
      this.isLoading = false;
      this.dataArr = res.data;
    }, error => {
      this.isLoading = false;
      this.toasterService.pop('error', 'Lỗi!', error);
    })
  }

  /**
   * Chỉ Hồ Minh Long mới được có quyền này :D "Supper Admin"
   */
  hasPermSuaXoa() {
    return this.curAuth && this.curAuth.tai_khoan.id === 'HT028'
  }

  /**
   * Chỉ những ai có quyền 'phan-quyen' mới được cập nhật
   */
  hasPerm() {
    return this.curAuth.phan_quyen.includes('phan-quyen');
  }

  update(_info) {
    this.tab = 'danh-sach';
    if (_info) {
      this.loadData();
    }
  }
}
