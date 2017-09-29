import { AppState } from './index';
import { ActionReducer } from '@ngrx/store';
import * as lopHocAction from '../actions/lop-hoc.action';

export type Action = lopHocAction.All;

/**
 * Define the state for Auth
 */
export interface LopHocState {
  loading: boolean;
  thong_tin: { [id: string]: any; };
  huynh_truong: any[];
  thieu_nhi: any[];
  error: string;
}

export const defaultLopHocState: LopHocState = {
  loading: false,
  thong_tin: null,
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
      return Object.assign({}, state, {
        loading: true,
        thong_tin: null,
        huynh_truong: [],
        thieu_nhi: [],
        error: null
      });
    }
    case lopHocAction.GETINFO_SUCC: {
      return Object.assign({}, state, {
        loading: false,
        thong_tin: action.payload,
        huynh_truong: action.payload.huynh_truong ? action.payload.huynh_truong : [],
        thieu_nhi: action.payload.hoc_vien ? action.payload.hoc_vien : [],
        error: null
      });
    }
    default: {
      return state;
    }
  }
};
