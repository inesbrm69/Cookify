import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";

@Injectable({
  providedIn: 'root'
})
export class QuantityFoodService {
  private apiUrl = 'http://13.60.53.129:8000/';

  constructor(private http: HttpClient) { }


}
