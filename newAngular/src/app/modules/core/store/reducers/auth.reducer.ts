import * as authAction from '../actions/auth.action';

export type Action = authAction.All;

/**
 * Define the state for Auth
 */
export interface AuthState {
  loading: boolean;
  tai_khoan: { [id: string]: any; };
  phan_quyen: any[];
  lop_hoc_hien_tai_id: number;
  khoa_hoc_hien_tai: { [id: string]: any; };
  error: string;
}

export const defaultState: AuthState = {
  loading: false,
  tai_khoan: null,
  phan_quyen: [],
  lop_hoc_hien_tai_id: 0, // Một số tài khoản không được xếp lớp. Vd: Ban Chấp Hành
  khoa_hoc_hien_tai: {
    id: null
  },
  error: null
};

/**
 * Define Reducer
 */
export function reducer(state = defaultState, action: Action): AuthState {
  switch (action.type) {
    case authAction.AUTH:
    case authAction.LOGOUT: {
      return Object.assign({}, defaultState, {
        loading: true,
      });
    }
    case authAction.AUTH_COMPLETED: {
      return Object.assign({}, defaultState, {
        tai_khoan: action.payload.tai_khoan,
        phan_quyen: action.payload.phan_quyen,
        lop_hoc_hien_tai_id: action.payload.lop_hoc_hien_tai_id ? action.payload.lop_hoc_hien_tai_id : 0,
        khoa_hoc_hien_tai: {
          id: action.payload.khoa_hoc_hien_tai_id
        },
      });
    }
    case authAction.GET_KHOAHOC: {
      return Object.assign({}, state, {
        khoa_hoc_hien_tai: action.khoaHoc,
      });
    }
    case authAction.AUTH_FAILED: {
      return Object.assign({}, defaultState, {
        error: action.err.error,
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
