import {Type} from 'class-transformer';
import City from '@/models/City';


export default class Research {
    content: string = '';
    distance: number = 10;
    @Type(() => City) cities: City[] = [];
    selectedCity: City | null = null;


    addCity(city: City): void
    {
        this.cities.push(city);
    }

    setSelectedCity(city: City | null = null): void
    {
        this.selectedCity = city;
    }
}