export const DROPOFF_PLACE_ID = {
  ENTRANCE: 1,
  GAS_METER: 2,
  GARAGE: 3,
  BICYCLE: 4,
  OTHER: 5,
}

export const DROPOFF_HISTORY_SEARCH_TYPE = {
  ALL: 'all',
  MYSELF: 'myself',
}

export function scrollToTop(element = window) {
  element.scrollTo(0, 0)
}
