export function isNumber(value: string|number): boolean
{
    if (value !== '') {
        return !isNaN(Number(value)) && isFinite(Number(value));
    }

    return false;
}
