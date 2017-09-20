import { ActionReducerMap } from '@ngrx/store';
import * as AuthR from './auth.reducer';

export interface AppState {
  auth: AuthR.AuthState,
}

export const reducer: ActionReducerMap<AppState> = {
  auth: AuthR.reducer
};
