import { Injectable, Pipe } from '@angular/core';
import { Observable, Subject, Subscription, pipe } from 'rxjs';
import { tap, map } from 'rxjs/operators';
import { URLSearchParams } from '@angular/http';

import { JwtAuthHttp } from '../../services/http-auth.service';
import { environment } from 'environments/environment';

declare global {
  interface Date {
    addDays(days): Date;
    toISO(): string;
  }
}

Date.prototype.addDays = function (days) {
  const date = new Date(this.valueOf());
  date.setDate(date.getDate() + days);
  return date;
}

Date.prototype.toISO = function () {
  const date = new Date(this.valueOf());
  const year: any = date.getFullYear();
  let month: any = date.getMonth() + 1;
  let dt: any = date.getDate();

  if (dt < 10) {
    dt = '0' + dt;
  }
  if (month < 10) {
    month = '0' + month;
  }

  return year + '-' + month + '-' + dt;
}

@Injectable()
export class DataService {
  private urlAPI = environment.apiURL + '/thiet-bi';
  constructor(
    private _http: JwtAuthHttp,
  ) {
  }

  private getSundays(startDate, stopDate) {
    const dateArray = new Array();
    let currentDate = new Date(startDate);
    const stop = new Date(stopDate);
    while (currentDate <= stop) {
      // Only add sunday
      if (currentDate.getDay() === 0) {
        dateArray.push(currentDate.toISO());
      }
      currentDate = currentDate.addDays(1);
    }
    return dateArray;
  }

  /**
   * Xóa thiết bị
   * @param id
   */
  delete(id): Observable<any> {
    return this._http.delete(this.urlAPI + '/' + id)
      .map(res => res.json())
      .switchMap(res => this.getList());
  }

  /**
   * Add new thiet bi
   * @param item
   */
  addNew(item): Observable<any> {
    const param = Object.assign({}, item, {
      trang_thai: item.trang_thai ? item.trang_thai : 'DA_TRA',
    });
    return this._http.post(this.urlAPI, param)
      .map(res => res.json())
      .switchMap(res => this.getList());
  }

  /**
   * Update item
   * @param item
   */
  update(id, item): Observable<any> {
    return this._http.post(this.urlAPI + '/' + id, item)
      .map(res => res.json())
      .switchMap(res => this.getList());
  }

  /**
   * Lấy danh sách thiết bị
   */
  getList(): Observable<any> {
    return this._http.get(this.urlAPI)
      .map((res: any) => res.json())
      .map((res: any) => res['data'])
      .map((res: any) => {
        return res.map(item => {
          if (item.tai_khoan) {
            item.tai_khoan_ten = item.tai_khoan.ten_thanh + ' - ' + item.tai_khoan.ho_va_ten;
          }
          return item;
        })
      })
      // Generate sundays array
      .map((res: any) => {
        let min = null;
        let max = null;

        // Chỉ quan tâm đến ngày nhưng thiết bị đang mượn
        res.filter(c => c.trang_thai === 'DANG_MUON').forEach(item => {
          if (!min || min > item.ngay_muon) {
            min = item.ngay_muon
          }
          if (!min || min > item.ngay_tra) {
            min = item.ngay_tra
          }

          if (!max || max < item.ngay_muon) {
            max = item.ngay_muon
          }
          if (!max || max < item.ngay_tra) {
            max = item.ngay_tra
          }
        });

        const dateArr = this.getSundays(min, max);
        return [res, dateArr];
      });
  }

  /**
   * Đăng ký thông tin thiết bị
   */
  regisDevice = (info, devices) => {
    const param = {
      info: Object.assign({}, info, {
        trang_thai: 'DANG_MUON',
      }),
      devices: devices,
    }
    return this._http.post(this.urlAPI + '/dang-ky', param)
      .map(res => res.json())
      .switchMap(res => this.getList());
  }

  /**
   * Load tai khoan HuynhTruong
   */
  loadTaiKhoan(): Observable<any> {
    const tkAPI = environment.apiURL + '/tai-khoan';
    const search = new URLSearchParams();
    search.set('trang_thai', 'HOAT_DONG');
    search.set('loai_tai_khoan', 'HUYNH_TRUONG,SOEUR,LINH_MUC');
    return this._http.get(tkAPI, { search })
      .pipe(
        map((res: any) => res.json()),
        map((res: any) => {
          return res['data'].map(c => {
            return {
              id: c.id,
              text: c.ten_thanh + ' - ' + c.ho_va_ten
            }
          });
        })
      );
  }
}
