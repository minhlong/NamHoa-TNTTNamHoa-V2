import { Component, OnInit, OnDestroy } from '@angular/core';
import { Observable, Subject, Subscription } from 'rxjs';
import { tap, map } from 'rxjs/operators';
import { Store } from '@ngrx/store';

import { DataService } from '../data.service';
import { AppState } from '../../../store/reducers/index';
import { AuthState } from '../../../store/reducers/auth.reducer';

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
  itemSelected: any;
  curAuth: AuthState;
  thietBiArr = []
  ngayArr = []

  pagingTN = {
    id: 'thietbiTable',
    itemsPerPage: 10,
    currentPage: 1,
  }

  constructor(
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

      // Trigger Search
      this.dataServ.getList().subscribe(res => {
        this.loadData$.next(res);
      }).unsubscribe();
      this.search$.next(null);
      this.changFilter$.next(this.filterTT);
    }, 0);

    this.sub$ = this.search$.debounceTime(400)
      .pipe(
        tap(c => this.isLoading = true),
      )
      .combineLatest(
        this.changFilter$,
        this.loadData$,
      ).pipe(
        // tap(c => console.log(c)),
        map(([searchStr, filterStatus, res]) => {
          return [searchStr, filterStatus, res[0], res[1]];
        }),
        // Map for search string
        map(([searchStr, filterStatus, thietbiArr, ngayArr]) => {
          let data = thietbiArr;
          if (searchStr) {
            data = thietbiArr.filter(item => {
              return item.ten.toLowerCase().indexOf(searchStr.toLowerCase()) !== -1 ||
                item.ten_tai_khoan.toLowerCase().indexOf(searchStr.toLowerCase()) !== -1;
            })
          }
          return [searchStr, filterStatus, data, ngayArr];
        }),
        // Map for filter trang thai
        map(([searchStr, filterStatus, thietbiArr, ngayArr]) => {
          let data = thietbiArr;
          if (filterStatus) {
            data = thietbiArr.filter(item => {
              return item.trang_thai === filterStatus;
            })
          }
          return [searchStr, filterStatus, data, ngayArr];
        }),
        // Map
        map(([searchStr, filterStatus, thietBiArr, ngayArr]) => [thietBiArr, ngayArr]),
      ).subscribe(([thietBiArr, ngayArr]) => {
        this.isLoading = false;
        this.thietBiArr = thietBiArr;
        this.ngayArr = ngayArr;
      })
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

    return true;
    // return false;
  }

  xoa(item) {
    this.dataServ.xoa(item.id).subscribe(res => {
      this.loadData$.next(res);
    }).unsubscribe();
  }

  luu(item) {
    this.dataServ.themMoi(item).subscribe(res => {
      this.loadData$.next(res);
    }).unsubscribe();
  }
}
