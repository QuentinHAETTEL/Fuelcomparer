<template>
    <form class="row" autocomplete="off" @submit.prevent="sendRequest()">
        <h2 class="mt-5 mb-10 text-center">{{ $t('dashboard.title') }}</h2>

        <div class="autocomplete" :class="{ 'autocomplete--open': showAutocompleteList }">
            <div class="form-floating">
                <input id="city" class="form-control" name="address" v-model="research.content" placeholder="" autocomplete="new-password" @input="findCities()" />
                <label for="city">{{ $t('dashboard.searchbarPlaceholder') }}</label>
                <span v-if="research.selectedCity" @click.stop="removeSelectedCity()">
                    <i class="autocomplete__icon fa-solid fa-xmark"></i>
                </span>
            </div>
            <div v-if="showAutocompleteList" class="list-group autocomplete__list">
                <button v-for="city in research.cities" class="list-group-item list-group-item-action autocomplete__item" @click.prevent="selectCity($event, city)">
                    <b>{{ city.postcode }}</b> - {{ city.name }}
                </button>
            </div>
        </div>

        <div class="accordion accordion-flush my-4">
            <div class="accordion-item bg-transparent">
                <div class="accordion-button collapsed bg-transparent" data-bs-toggle="collapse" data-bs-target="#more-options" aria-expanded="false">
                    {{ $t('dashboard.options.showMore') }}
                </div>
                <div id="more-options" class="accordion-collapse collapse">
                    <label for="distance-range" class="form-label">{{ $t('dashboard.options.distance') }}</label>
                    <div class="position-relative mb-5">
                        <input id="distance-range" class="form-range" type="range" min="0" :max="distanceMax" :value="distanceSelected" @input="changeDistance($event.target.value)" />
                        <output class="badge bg-primary range__badge" :style="distanceBadgePosition">{{ distanceText }}</output>
                    </div>

                    <label for="fuel-type" class="form-label">{{ $t('dashboard.options.fuelType') }}</label>
                    <select id="fuel-type" class="form-select">
                        <option value="gasoline" selected>{{ $t('dashboard.options.gasoline') }}</option>
                        <option value="diesel">{{ $t('dashboard.options.diesel') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-3 text-center">
            <button class="btn btn-primary" type="submit" :disabled="!canValidate">{{ $t('dashboard.search') }}</button>
        </div>
    </form>
</template>


<script lang="ts">
import {Component, Vue} from 'vue-property-decorator';
import {config} from '@/config';
import {isNumber} from '@/mixin';
import Research from '@/models/Research';
import City from '@/models/City';

@Component
export default class Dashboard extends Vue {

    public research: Research = new Research();
    private urlAddressApi: string = 'https://geo.api.gouv.fr/communes'; // URL API doc : https://geo.api.gouv.fr/decoupage-administratif/communes
    private urlApi: string = '/api/search';
    private distanceMax: number = 5;
    private distanceSelected: number = 1;

    get showAutocompleteList(): boolean {
        return this.research.cities.length > 0 && this.research.selectedCity === null;
    }

    get distanceText(): string {
        return '<' + this.research.distance + ' km';
    }

    get distanceBadgePosition(): string {
        const percentage = Math.round((this.distanceSelected) * 10000 / this.distanceMax) / 100;
        const ratio = 0.5 - percentage/100;
        return `left: calc(${percentage}% + (${ratio*8*this.distanceText.length}px));`;
    }

    get canValidate(): boolean {
        return this.research.selectedCity !== null;
    }

    createCitiesQuery(value: string, type: string = 'name', boost: string = 'population', limit: string|number = 10): string {
        let query = this.urlAddressApi;
        if (type === 'postcode') {
            query += '?codePostal=';
        } else {
            query += '?nom=';
            value = value.replace(' ', '-');
        }

        return query + value.toLowerCase() + '&boost=' + boost + '&limit=' + limit;
    }

    findCities(): void {
        if (this.research.content.length >= 3) {
            this.findCitiesByValue(this.createCitiesQuery(this.research.content));
        }
        if (isNumber(this.research.content) && this.research.content.length === 5) {
            this.findCitiesByValue(this.createCitiesQuery(this.research.content, 'postcode'))
        }
    }

    findCitiesByValue(query: string): City[] {
        fetch(query)
            .then(response => response.json())
            .then(data => {
                this.research.cities = [];
                for (let city of data) {
                    this.research.addCity(new City(city.nom, city.codesPostaux[0]));
                }
                return this.research.cities;
            });
        return this.research.cities;
    }

    selectCity(event: Event, city: City): void {
        this.research.setSelectedCity(city);
        this.research.content = city.postcode + ' - ' + city.name;
    }

    removeSelectedCity(): void {
        this.research.setSelectedCity();
        this.research.content = '';
        this.research.cities = [];
    }

    changeDistance(index: string): void
    {
        this.distanceSelected = parseInt(index);
        this.research.distance = (parseInt(index) * 5) + 5;
    }

    sendRequest(): void {
        if (!this.canValidate) {
            console.log('ERROR')
        }

        fetch(config.baseURL + this.urlApi, {
            method: 'POST',
            body: JSON.stringify({
                data: this.research
            })
        })
            .then(response => response.json())
            .then(data => {
                console.log(data);
            });
    }

}
</script>


<style lang="scss">
.range__badge {
    position: absolute;
    top: 30px;
    transform: translate(-50%, 0);
}
</style>