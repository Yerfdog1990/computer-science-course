package complexity.constant;

public class SimpleMathematicalFunctions {

    public static double calculateCircleArea(double radius) {
        return Math.PI * Math.pow(radius, 2);
    }

    public static double calculateCircleCircumference(double radius) {
        return 2 * Math.PI * radius;
    }

    public static double calculateRectangleArea(double length, double width) {
        return length * width;
    }

    public static double calculateTrianglePerimeter(double side1, double side2, double side3) {
        return side1 + side2 + side3;
    }

    public static void main(String[] args) {
        double radius = 5.0;
        double length = 10.0;
        double width = 5.0;
        double side1 = 3.0;
        double side2 = 4.0;
        double side3 = 5.0;

        System.out.println("Circle Area: " + calculateCircleArea(radius));
        System.out.println("Circle Circumference: " + calculateCircleCircumference(radius));
        System.out.println("Rectangle Area: " + calculateRectangleArea(length, width));
        System.out.println("Triangle Perimeter: " + calculateTrianglePerimeter(side1, side2, side3));
    }
}
