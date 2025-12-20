import { computed, onMounted, ref, watch } from "vue";

export function useAddress() {
    const regions = ref([]);
    const provinces = ref([]);
    const cities = ref([]);
    const barangays = ref([]);

    const selectedRegion = ref("");
    const selectedProvince = ref("");
    const selectedCity = ref("");
    const selectedBarangay = ref("");

    const isLoadingRegions = ref(false);
    const isLoadingProvinces = ref(false);
    const isLoadingCities = ref(false);
    const isLoadingBarangays = ref(false);

    const isNcr = computed(() => {
        const region = regions.value.find(
            (r) => r.name === selectedRegion.value
        );
        return region
            ? region.code === "130000000" || region.name.includes("NCR")
            : false;
    });

    async function fetchRegions() {
        isLoadingRegions.value = true;
        try {
            const res = await fetch("https://psgc.gitlab.io/api/regions/");
            regions.value = await res.json();
        } catch (err) {
            console.error("Failed to fetch regions:", err);
        } finally {
            isLoadingRegions.value = false;
        }
    }

    async function fetchProvinces(regionCode) {
        isLoadingProvinces.value = true;
        try {
            const res = await fetch(
                `https://psgc.gitlab.io/api/regions/${regionCode}/provinces/`
            );
            provinces.value = await res.json();
        } catch (err) {
            console.error("Failed to fetch provinces:", err);
        } finally {
            isLoadingProvinces.value = false;
        }
    }

    async function fetchCities(code, ncr = false) {
        isLoadingCities.value = true;
        const url = ncr
            ? `https://psgc.gitlab.io/api/regions/${code}/cities-municipalities/`
            : `https://psgc.gitlab.io/api/provinces/${code}/cities-municipalities/`;

        try {
            const res = await fetch(url);
            cities.value = await res.json();
        } catch (err) {
            console.error("Failed to fetch cities:", err);
        } finally {
            isLoadingCities.value = false;
        }
    }

    async function fetchBarangays(cityCode) {
        isLoadingBarangays.value = true;
        try {
            const res = await fetch(
                `https://psgc.gitlab.io/api/cities-municipalities/${cityCode}/barangays/`
            );
            barangays.value = await res.json();
        } catch (err) {
            console.error("Failed to fetch barangays:", err);
        } finally {
            isLoadingBarangays.value = false;
        }
    }

    watch(selectedRegion, async (val) => {
        selectedProvince.value = "";
        selectedCity.value = "";
        selectedBarangay.value = "";
        provinces.value = [];
        cities.value = [];
        barangays.value = [];

        if (!val) return;

        const region = regions.value.find((r) => r.name === val);
        if (!region) return;

        if (isNcr.value) {
            await fetchCities(region.code, true);
        } else {
            await fetchProvinces(region.code);
        }
    });

    watch(selectedProvince, async (val) => {
        if (isNcr.value || !val) return;

        selectedCity.value = "";
        selectedBarangay.value = "";
        cities.value = [];
        barangays.value = [];

        const province = provinces.value.find((p) => p.name === val);
        if (province) await fetchCities(province.code);
    });

    watch(selectedCity, async (val) => {
        if (!val) return;

        selectedBarangay.value = "";
        barangays.value = [];

        const city = cities.value.find((c) => c.name === val);
        if (city) await fetchBarangays(city.code);
    });

    onMounted(fetchRegions);

    return {
        regions,
        provinces,
        cities,
        barangays,
        selectedRegion,
        selectedProvince,
        selectedCity,
        selectedBarangay,
        isNcr,
        isLoadingRegions,
        isLoadingProvinces,
        isLoadingCities,
        isLoadingBarangays,
    };
}
