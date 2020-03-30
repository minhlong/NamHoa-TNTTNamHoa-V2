import { ActivatedRoute } from '@angular/router';
import { Store } from '@ngrx/store';
import { Component, OnInit, Input, Output, EventEmitter, OnDestroy } from '@angular/core';
import { ToasterService } from 'angular2-toaster';
import { FormBuilder, FormGroup, Validators, FormArray } from '@angular/forms';

import { HttpClient } from '@angular/common/http'; // import { JwtAuthHttp } from '../../../../../services/http-auth.service';
import { environment } from 'src/environments/environment';
import { AppState } from '../../../../../store/reducers';

@Component({
  selector: 'app-form-diem-so',
  templateUrl: './form-diem-so.component.html',
  styleUrls: ['./form-diem-so.component.scss']
})
export class FormDiemSoComponent implements OnInit, OnDestroy {
  @Input() apiData;
  @Input() lanKT;
  @Input() thieuNhiArr;
  @Output() updateInfo = new EventEmitter();

  private urlAPI = environment.apiURL + '/lop-hoc';

  pagingTN = {
    id: 'tnTable',
    itemsPerPage: 10,
    currentPage: 1,
  }

  lopHocID: number;
  isLoading: boolean;

  formThieuNhi: FormArray;
  formGroup: FormGroup;
  error: any;
  sub$: any;

  constructor(
    private activeRoute: ActivatedRoute,
    private store: Store<AppState>,
    private toasterService: ToasterService,
    private _fb: FormBuilder,
    private _http: HttpClient,
  ) {
    this.sub$ = this.activeRoute.parent.params.subscribe(params => {
      this.lopHocID = params['id'];
    });
  }

  ngOnInit() {
    // Tạo form để submit lên server
    this.formThieuNhi = this._fb.array(this.initHocLuc());
    this.formGroup = this._fb.group({
      dot: this.apiData.dot,
      lan: this.lanKT,
      thieu_nhi: this.formThieuNhi,
    });
  }

  ngOnDestroy() {
    this.sub$.unsubscribe();
  }

  /**
   * Tạo form cho từng học viên
   */
  private initHocLuc() {
    const tmpArr = [];
    this.thieuNhiArr.forEach(_tn => {
      const tmpTn = this.findHocLuc(_tn);
      tmpArr.push(this._fb.group({
        id: _tn.id,
        ho_va_ten: _tn.ho_va_ten,
        diem: tmpTn.diem,
      }));
    });

    return tmpArr;
  }

  /**
   * Tìm học viên để fill dữ liệu vào form
   * @param tn Thiếu Nhi
   */
  private findHocLuc(tn) {
    let res;
    if (this.apiData) {
      res = this.apiData.data.find(c => c.tai_khoan_id === tn.id && c.lan === this.lanKT);
    }
    return res ? res : {};
  }

  /**
   * Chỉ cho nhập số
   */
  _keyPress(event: any) {
    const pattern = /[0-9\.]/;
    const inputChar = String.fromCharCode(event.charCode);

    if (!pattern.test(inputChar)) {
      // invalid character, prevent input
      event.preventDefault();
    }
  }

  /**
   * Lưu điểm
   *    Nếu OK -> quay lại trang chính
   *    Nếu lỗi -> hiện lỗi
   */
  save() {
    const _url = this.urlAPI + '/' + this.lopHocID + '/hoc-luc';
    this.isLoading = true;

    this._http.post(_url, this.formGroup.value).map(res => res.json()).subscribe(res => {
      this.isLoading = false;
      this.updateInfo.emit(res);
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

  /**
   * Thoát -> quay lại trang chính
   */
  cancel() {
    this.updateInfo.emit(null);
  }
}
