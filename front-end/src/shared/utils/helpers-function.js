export function getUTCDateString(dateObj) {
  const year = dateObj.getUTCFullYear();
  const month = String(dateObj.getUTCMonth() + 1).padStart(2, '0'); // Month is 0-indexed
  const day = String(dateObj.getUTCDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}
export function getCETDateString(dateObj) {
  const year = dateObj.getFullYear();
  const month = String(dateObj.getMonth() + 1).padStart(2, '0'); // Month is 0-indexed
  const day = String(dateObj.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

export function getUTCTimeString(dateObj) {
  const hours = String(dateObj.getUTCHours()).padStart(2, '0');
  const minutes = String(dateObj.getUTCMinutes()).padStart(2, '0');
  return `${hours}:${minutes}`;
}

export function readablePrice(price) {
  return String(parseInt(price/100)) + '.' + String(price % 100).padStart(2, '0');
}