import { Pipe, PipeTransform } from '@angular/core';
import { appConst } from './shared/constance';

@Pipe({
  name: 'loaiTK'
})
export class LoaiTK implements PipeTransform {

  transform(value: any, args?: any): any {
    const _tmp = appConst.find(el => el.loai_du_lieu === 'LOAI_TAI_KHOAN' && el.ky_hieu === value);
    if (_tmp) {
      return _tmp.ten
    }
    return value;
  }
}

@Pipe({
  name: 'trangThai'
})
export class TrangThai implements PipeTransform {

  transform(value: any, args?: any): any {
    const _tmp = appConst.find(el => el.loai_du_lieu === 'TRANG_THAI' && el.ky_hieu === value);
    if (_tmp) {
      return _tmp.ten
    }
    return value;
  }
}

@Pipe({
  name: 'ngay'
})
export class HienThiNgay implements PipeTransform {

  transform(value: any, args?: any): any {
    if (value === '0000-00-00') {
      return null;
    }
    if (value) {
      return value.replace(/(.+)[-|\/](.+)[-|\/](.+)/i, '$3-$2-$1');
    }
    return value;
  }
}
