import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'objectKeys'
})
export class ObjectKeysPipe implements PipeTransform {

  transform(value, args: string[]): any {
    const keys = [];
    for (const key in value) {
      if (value[key]) {
        keys.push({ key: key, value: value[key] });
      }
    }
    return keys;
  }
}
