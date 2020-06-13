import { Action } from '@ngrx/store';

export const GETINFO = '[Lop Hoc] Get Info';
export const GETINFO_SUCC = '[Lop Hoc] Get Info Success';
export const ERR = '[Lop Hoc] Lỗi';

export class GetLopInfo implements Action {
  readonly type = GETINFO;
  constructor(public id) { }
}

export class GetLopInfoSucc implements Action {
  readonly type = GETINFO_SUCC;
  constructor(public payload) { }
}

export class Err implements Action {
  readonly type = ERR;
  constructor(public error) { }
}

export type All = GetLopInfo | GetLopInfoSucc | Err;
