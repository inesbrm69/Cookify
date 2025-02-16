import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'timeFormat'
})
export class TimeFormatPipe implements PipeTransform {

  transform(value: number): string {
    if(value < 60){
      return `${value.toString().padStart(2, '0')} minutes`;
    }
    if (!value && value !== 0) return '';

    const hours = Math.floor(value / 60);
    const minutes = value % 60;

    return `${hours}h${minutes.toString().padStart(2, '0')}`;
  }
}
