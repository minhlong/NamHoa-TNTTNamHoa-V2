import { ActionReducerMap } from '@ngrx/store';
import * as authReducer from './auth.reducer';
import * as lopHocReducer from './lop-hoc.reducer';

export interface AppState {
  auth: authReducer.AuthState;
  lop_hoc: lopHocReducer.LopHocState;
}

export const reducer: ActionReducerMap<AppState> = {
  auth: authReducer.reducer,
  lop_hoc: lopHocReducer.reducer,
};
