import { AppState } from './index';
import { ActionReducer } from '@ngrx/store';
import * as authAction from '../actions/auth.action';

export type Action = authAction.All;

/**
 * Define the state for Auth
 */
export interface AuthState {
  loading: boolean;
  tai_khoan: { [id: string]: any; };
  phan_quyen: any[],
  lop_hoc_hien_tai_id: string,
  khoa_hoc_hien_tai_id: number,
  error: string;
}

export const defaultState: AuthState = {
  loading: false,
  tai_khoan: null,
  phan_quyen: [],
  lop_hoc_hien_tai_id: null,
  khoa_hoc_hien_tai_id: null,
  error: null
};

/**
 * Define Reducer
 */
export function reducer(state = defaultState, action: Action): AuthState {
  switch (action.type) {
    case authAction.AUTH:
    case authAction.LOGOUT: {
      return Object.assign({}, state, {
        tai_khoan: null,
        phan_quyen: [],
        lop_hoc_hien_tai_id: null,
        khoa_hoc_hien_tai_id: null,
        loading: true,
        error: null
      });
    }
    case authAction.AUTH_COMPLETED: {
      return Object.assign({}, state, {
        tai_khoan: action.payload.tai_khoan,
        phan_quyen: action.payload.phan_quyen,
        lop_hoc_hien_tai_id: action.payload.lop_hoc_hien_tai_id,
        khoa_hoc_hien_tai_id: action.payload.khoa_hoc_hien_tai_id,
        loading: false,
        error: null
      });
    }
    case authAction.AUTH_FAILED: {
      return Object.assign({}, state, {
        tai_khoan: null,
        phan_quyen: [],
        lop_hoc_hien_tai_id: null,
        khoa_hoc_hien_tai_id: null,
        loading: false,
        error: action.err,
      });
    }
    case authAction.LOGOUT_SUCCESS: {
      return defaultState;
    }
    default: {
      return state;
    }
  }
};
