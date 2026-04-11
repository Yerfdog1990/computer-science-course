package basic_sorting;

public class BubbleSort {
    static int i, j, temp;
    static boolean swapped;
    public static void bubbleSort(int[] array, int size){
        for (i = 0; i < size - 1; i++){
            swapped = false;

            for (j = 0; j < size - i - 1; j++){
                if (array[j] > array[j + 1]){
                    temp = array[j];
                    array[j] = array [j + 1];
                    array[j + 1] = temp;
                    swapped = true;
                }
            }
            if (!swapped){
                break;
            }
        }
    }
    public static void printSortedArray(int[] array){
        for (int num: array){
            System.out.print(num + ", ");
        }
    }

    static void main() {
        int[] array = {2, 9, 8, 1, 7, 3, 10, 4, 5};

        System.out.println("Unsorted array:");
        for (int num : array){
            System.out.print(num + ", ");
        }
        int size = array.length;
        bubbleSort(array, size);

        System.out.println("\nPrint sorted array");
        printSortedArray(array);
    }
}
