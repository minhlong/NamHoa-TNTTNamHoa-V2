import { ActivatedRoute } from '@angular/router';
import { Store } from '@ngrx/store';
import { Component, OnInit, Input, Output, EventEmitter, OnDestroy } from '@angular/core';
import { ToasterService } from 'angular2-toaster';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { URLSearchParams } from '@angular/http';
import { Subject } from 'rxjs';

import { JwtAuthHttp } from '../../../../../services/http-auth.service';
import { environment } from 'environments/environment';
import { AppState } from '../../../../../store/reducers';
import { bodauTiengViet } from '../../../../../_helpers';
import { ngay } from '../../../../shared/utities.pipe';
import { GetLopInfo } from '../../../../../store/actions/lop-hoc.action';

@Component({
  selector: 'app-ngay-them-suc',
  templateUrl: './ngay-them-suc.component.html',
  styleUrls: ['./ngay-them-suc.component.scss']
})
export class NgayThemSucComponent implements OnDestroy {
  @Output() updateInfo = new EventEmitter();
  private urlAPI = environment.apiURL + '/tai-khoan/ngay-them-suc';

  lopHocID: number;
  isLoading: boolean;
  error: any;

  thieuNhiArr = [];
  filter$ = new Subject<any>();

  pagingHT = {
    id: 'htTable',
    itemsPerPage: 10,
    currentPage: 1,
  }

  maskOption = {
    mask: [/[0-9]/, /[0-9]/, '-', /[0-9]/, /[0-9]/, '-', /[0-9]/, /[0-9]/, /[0-9]/, /[0-9]/],
    guide: true,
  }

  sub$: any;
  subCb$: any;
  ngay: any;

  constructor(
    private activeRoute: ActivatedRoute,
    private store: Store<AppState>,
    private toasterService: ToasterService,
    private formBuilder: FormBuilder,
    private _http: JwtAuthHttp,
  ) {
    this.isLoading = true;
    this.ngay = ngay(new Date().toJSON().slice(0, 10));

    this.sub$ = this.activeRoute.parent.params.subscribe(params => {
      this.lopHocID = params['id'];
    });

    this.subCb$ = this.filter$.debounceTime(400)
      .combineLatest(this.store.select((state: AppState) => state.lop_hoc.thieu_nhi))
      .subscribe(([_str, _thieuNhiArr]) => {
        this.isLoading = false;
        this.thieuNhiArr = _thieuNhiArr.map(x => Object.assign({}, x)).filter(el => {
          const _tmpA = bodauTiengViet(el.ho_va_ten);
          const _tmpB = bodauTiengViet(_str);
          return _tmpA.toLowerCase().indexOf(_tmpB.toLowerCase()) !== -1
        });
      });

    this.filter$.next(''); // Trigger Search
  }

  ngOnDestroy() {
    this.filter$.complete();
    this.sub$.unsubscribe();
    this.subCb$.unsubscribe();
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

  save() {
    const _url = this.urlAPI;
    const _par = Object.assign({}, {
      ngay_them_suc: ngay(this.ngay),
      tai_khoan: this.thieuNhiArr.filter((_el) => {
        return _el.checked === true;
      }).map(c => c.id),
    });
    this.isLoading = true;
    this._http.post(_url, _par).map(res => res.json()).subscribe(res => {
      this.store.dispatch(new GetLopInfo(this.lopHocID));
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
