import { Component, OnInit, OnDestroy } from '@angular/core';
import { Observable, Subject, Subscription } from 'rxjs';
import { tap, map } from 'rxjs/operators';
import { Store } from '@ngrx/store';

import { DataService } from '../data.service';
import { AppState } from '../../../store/reducers/index';
import { AuthState } from '../../../store/reducers/auth.reducer';
import { ToasterService } from 'angular2-toaster';
import { ngay } from 'app/modules/shared/utities.pipe';

declare let jQuery: any;

@Component({
  selector: 'app-danh-sach',
  templateUrl: './danh-sach.component.html',
  styleUrls: ['./danh-sach.component.scss'],
})
export class DanhSachComponent implements OnDestroy {
  sub$: Subscription;
  subAuth$: Subscription;
  loadData$ = new Subject<any>();
  search$ = new Subject<any>();
  changFilter$ = new Subject<any>();

  isLoading = true;
  filterTT = '';
  itemSelected: any = {};
  curAuth: AuthState;
  thietBiArr = []; // Tất cả các thiết bị
  thietBiDaTraArr = []; // Thiết bị đã trả (còn trong kho)
  ngayArr = [];
  taiKhoanArr = [];
  pagingTN = {
    id: 'thietbi-ds-Table',
    itemsPerPage: 10,
    currentPage: 1,
  }

  maskOption = {
    mask: [/[0-9]/, /[0-9]/, '-', /[0-9]/, /[0-9]/, '-', /[0-9]/, /[0-9]/, /[0-9]/, /[0-9]/],
    guide: true,
  }

  constructor(
    private toasterService: ToasterService,
    private store: Store<AppState>,
    public dataServ: DataService,
  ) {
    this.subAuth$ = this.store.select((state: AppState) => state.auth).subscribe(res => {
      this.curAuth = res;
    });

    setTimeout(() => {
      // Tooltips
      jQuery('.tooltip-thietbi').tooltip({
        selector: '[data-toggle=tooltip]',
        container: 'body'
      });

      // Load device data
      this.getAllDevice();

      // Load tai khoan huynh truong data
      this.dataServ.loadTaiKhoan().subscribe(res => {
        this.taiKhoanArr = res;
      });

      // Trigger
      this.search$.next(null);
      this.changFilter$.next(this.filterTT);
    }, 0);

    this.sub$ = this.search$.debounceTime(400)
      .pipe(
        // Mỗi lần gõ sẽ cho hiệu ứng loading
        tap(c => this.isLoading = true),
      )
      .combineLatest(
        this.changFilter$,
        this.loadData$,
      ).pipe(
        // Sau khi lấy dữ liệu từ server thì maping lại dữ liệu
        map(([searchStr, filterStatus, res]) => {
          return [searchStr, filterStatus, res[0], res[1]];
        }),
        // Lọc dữ liệu từ thanh tìm kiếm
        map(([searchStr, filterStatus, thietbiArr, ngayArr]) => {
          let data = thietbiArr;
          if (searchStr) {
            data = thietbiArr.filter(item => {
              if (item.ten.toLowerCase().indexOf(searchStr.toLowerCase()) !== -1) {
                return true;
              }

              if (item.hasOwnProperty('tai_khoan_ten') && item.tai_khoan_ten.toLowerCase().indexOf(searchStr.toLowerCase()) !== -1) {
                return true;
              }
              return false;
            })
          }
          return [searchStr, filterStatus, data, ngayArr];
        }),
        // Lọc dữ liệu từ thanh trạng thái
        map(([searchStr, filterStatus, thietbiArr, ngayArr]) => {
          let data = thietbiArr;
          if (filterStatus) {
            data = thietbiArr.filter(item => {
              return item.trang_thai === filterStatus;
            })
          }
          return [searchStr, filterStatus, data, ngayArr];
        }),
        // Maping lại dữ liệu
        map(([searchStr, filterStatus, thietBiArr, ngayArr]) => [thietBiArr, ngayArr]),
      ).subscribe(([thietBiArr, ngayArr]) => {
        this.isLoading = false;
        this.thietBiArr = thietBiArr;

        // Lọc các thiết bị đã trả (không phải đang mượn)
        this.thietBiDaTraArr = this.thietBiArr.filter(c => c.trang_thai !== 'DANG_MUON')
          .map(c => {
            // Thêm thuộc tính checked
            return Object.assign(c, { checked: false })
          });
        this.ngayArr = ngayArr;
      })
  }

  getAllDevice() {
    this.dataServ.getList().subscribe(res => {
      this.loadData$.next(res);
    });
  }

  private showError(_err, isReload = true) {
    if (isReload) {
      this.getAllDevice();
    } else {
      this.isLoading = false;
    }

    if (typeof _err === 'string') {
      this.toasterService.pop('error', 'Lỗi!', _err);
    } else {
      for (const _field in _err) {
        if (_err.hasOwnProperty(_field)) {
          _err[_field].forEach(_mess => {
            this.toasterService.pop('error', 'Lỗi!', _mess);
          });
        }
      }
    }
  }

  ngOnDestroy() {
    this.subAuth$.unsubscribe();
    this.sub$.unsubscribe();
    this.search$.complete();
    this.loadData$.complete();
    this.changFilter$.complete();
  }

  hasPerm() {
    if (this.curAuth.phan_quyen.includes('thiet-bi')) {
      return true;
    }

    return false;
  }

  convertDay(value) {
    return ngay(value);
  }

  selectedHuynhTruong(item, obj) {
    item.tai_khoan_id = obj.id;
    item.tai_khoan_ten = obj.text;
  }

  /**
   * Xóa thiết bị
   * @param item
   */
  xoa(item) {
    this.isLoading = true;
    this.dataServ.delete(item.id).subscribe(res => {
      this.loadData$.next(res);
    });
  }

  /**
   * Thêm/Sửa thông tin thiết bị
   * @param item
   */
  luu(item) {
    this.isLoading = true;
    if (item.id) {
      // Update thiet bi
      this.dataServ.update(item.id, item).subscribe(res => {
        this.loadData$.next(res);
      }, _err => this.showError(_err));
    } else {
      // Insert new
      this.dataServ.addNew(item).subscribe(res => {
        this.loadData$.next(res);
      }, _err => this.showError(_err, false));
    }
  }

  /**
   * Đăng ký mượn thiết bị - Start
   * @param item
   */
  dangKy(item) {
    this.isLoading = true;
    const deviceIDs = this.thietBiDaTraArr.filter(c => c.checked === true).map(c => c.id);
    this.dataServ.regisDevice(item, deviceIDs).subscribe(res => {
      this.loadData$.next(res);
    }, _err => this.showError(_err, false));
  }

  checkboxChange(id, value) {
    const found = this.thietBiDaTraArr.find(c => c.id === id)
    found.checked = value;
  }
  // Đăng ký mượn thiết bị - End
}
