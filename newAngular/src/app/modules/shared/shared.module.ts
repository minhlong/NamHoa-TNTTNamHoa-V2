import { CommonModule } from '@angular/common';
import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';

// import { ListErrorsComponent } from './list-errors.component';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    // ReactiveFormsModule,
    // HttpClientModule,
    // RouterModule
  ],
  declarations: [
    // ListErrorsComponent,
  ],
  exports: [
    CommonModule,
    FormsModule,
  ]
})
export class SharedModule {}
