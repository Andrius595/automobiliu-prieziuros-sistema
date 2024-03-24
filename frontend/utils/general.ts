export const timestampToDate = (timestamp: string) => {
    return (new Date(timestamp)).toISOString().split('T')[0];
}