import { AppState } from '.';
import { ActionReducer } from '@ngrx/store';
import * as lopHocAction from '../actions/lop-hoc.action';

export type Action = lopHocAction.All;

/**
 * Define the state for Auth
 */
export interface LopHocState {
  thong_tin: { [id: string]: any; };
  huynh_truong: any[];
  thieu_nhi: any[];
  error: string;
}

export const defaultLopHocState: LopHocState = {
  thong_tin: {
    khoa_hoc_id: null,
  },
  huynh_truong: [],
  thieu_nhi: [],
  error: null
};

/**
 * Define Reducer
 */
export function reducer(state = defaultLopHocState, action: Action): LopHocState {
  switch (action.type) {
    case lopHocAction.GETINFO: {
      return defaultLopHocState;
    }
    case lopHocAction.GETINFO_SUCC: {
      return Object.assign({}, defaultLopHocState, {
        thong_tin: action.payload,
        huynh_truong: action.payload.huynh_truong ? action.payload.huynh_truong : [],
        thieu_nhi: action.payload.hoc_vien ? action.payload.hoc_vien : [],
      });
    }
    case lopHocAction.ERR: {
      return Object.assign({}, defaultLopHocState, {
        error: action.error
      });
    }
    default: {
      return state;
    }
  }
};
