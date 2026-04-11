package basic_sorting;

public class SelectionSort {
    public static int temp;
    public static void selectionSort(int[] array, int size){
        for (int i = 0; i < size -1; i++){
            int min_index = i;

            for (int j = i + 1; j < size; j++){
                if(array[j] < array[min_index]){
                    min_index = j;
                }
            }
            temp = array[i];
            array[i] = array[min_index];
            array[min_index] = temp;
        }
    }
     public static void printSortedArray (int[] array){
        for (int val : array){
            System.out.print(val+ ", ");
        }
     }

    static void main() {
        int[] array = {2, 9, 8, 1, 7, 3, 10, 4, 5};

        System.out.println("Unsorted array:");
        for (int num : array){
            System.out.print(num + ", ");
        }
        int size = array.length;
        selectionSort(array, size);

        System.out.println("\nPrint sorted array");
        printSortedArray(array);
    }
}
