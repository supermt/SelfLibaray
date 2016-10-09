# java 弱引用

当一个对象只具有软引用，则内存空间足够时，垃圾回收期就不会回收它，就会就收这些对象的内存。只要垃圾回收器没有回收，该对象就可以被程序使用。软引用可以和引用队列联合使用。如果软引用对象被垃圾回收期回收，Java虚拟机就会把这个软引用加入到与之关联的引用队列汇总。

## 使用软引用构件敏感数据的缓存

我们将使用一个Java语言实现的雇员信息查询系统查询存储在磁盘文件或者数据库中的雇员人事档案信息。作为一个用户，我们完全有可能需要回头去查看几分钟甚至几秒钟前查看过的雇员档案信息(同样，我们在浏览WEB页面的时候也经常会使用“后退”按钮)。这时我们通常会有两种程序实现方式:一种是把过去查看过的雇员信息保存在内存中，每一个存储了雇员档案信息的Java对象的生命周期贯穿整个应用程序始终;另一种是当用户开始查看其他雇员的档案信息的时候，把存储了当前所查看的雇员档案信息的Java对象结束引用，使得垃圾收集线程可以回收其所占用的内存空间，当用户再次需要浏览该雇员的档案信息的时候，重新构建该雇员的信息。很显然，第一种实现方法将造成大量的内存浪费，而第二种实现的缺陷在于即使垃圾收集线程还没有进行垃圾收集，包含雇员档案信息的对象仍然完好地保存在内存中，应用程序也要重新构建一个对象。我们知道，访问磁盘文件、访问网络资源、查询数据库等操作都是影响应用程序执行性能的重要因素，如果能重新获取那些尚未被回收的Java对象的引用，必将减少不必要的访问，大大提高程序的运行速度。

## 使用SoftReference保存对一个Java对象的软引用

SoftReference的特点是它的一个实例保存对一个Java对象的软引用，该软引用的存在不妨碍垃圾收集线程对该Java对象的回收。也就是说，一旦SoftReference保存了对一个Java对象的软引用后，在垃圾线程对这个Java对象回收前，SoftReference类所提供的get()方法返回Java对象的强引用。另外，一旦垃圾线程回收该Java对象之后，get()方法将返回null。

```java
MyObject aRef = new  MyObject(); 
SoftReference aSoftRef=new SoftReference(aRef); 
```
此时，对于这个MyObject对象，有两个引用路径，一个是来自SoftReference对象的软引用，一个来自变量aReference的强引用，所以这个MyObject对象是强可及对象。

随即，我们可以结束aReference对这个MyObject实例的强引用:
```java
aRef = null; 
```

此后，这个MyObject对象成为了软可及对象。如果垃圾收集线程进行内存垃圾收集，并不会因为有一个SoftReference对该对象的引用而始终保留该对象。Java虚拟机的垃圾收集线程对软可及对象和其他一般Java对象进行了区别对待:软可及对象的清理是由垃圾收集线程根据其特定算法按照内存需求决定的。也就是说，垃圾收集线程会在虚拟机抛出OutOfMemoryError之前回收软可及对象，而且虚拟机会尽可能优先回收长时间闲置不用的软可及对象，对那些刚刚构建的或刚刚使用过的“新”软可反对象会被虚拟机尽可能保留。在回收这些对象之前，我们可以通过:
```java
MyObject anotherRef=(MyObject)aSoftRef.get(); 
```
重新获得对该实例的强引用。而回收之后，调用get()方法就只能得到null了。

## 使用ReferenceQueue清除失去了软引用对象的SoftReference
作为一个Java对象，SoftReference对象除了具有保存软引用的特殊性之外，也具有Java对象的一般性。所以，当软可及对象被回收之后，虽然这个SoftReference对象的get()方法返回null,但这个SoftReference对象已经不再具有存在的价值，需要一个适当的清除机制，避免大量SoftReference对象带来的内存泄漏。在java.lang.ref包里还提供了ReferenceQueue。如果在创建SoftReference对象的时候，使用了一个ReferenceQueue对象作为参数提供给SoftReference的构造方法，如:
```java
ReferenceQueue queue = new  ReferenceQueue(); 
SoftReference  ref=new  SoftReference(aMyObject, queue); 
```

那么当这个SoftReference所软引用的aMyOhject被垃圾收集器回收的同时，ref所强引用的SoftReference对象被列入ReferenceQueue。也就是说，ReferenceQueue中保存的对象是Reference对象，而且是已经失去了它所软引用的对象的Reference对象。另外从ReferenceQueue这个名字也可以看出，它是一个队列，当我们调用它的poll()方法的时候，如果这个队列中不是空队列，那么将返回队列前面的那个Reference对象。
在任何时候，我们都可以调用ReferenceQueue的poll()方法来检查是否有它所关心的非强可及对象被回收。如果队列为空，将返回一个null,否则该方法返回队列中前面的一个Reference对象。利用这个方法，我们可以检查哪个SoftReference所软引用的对象已经被回收。于是我们可以把这些失去所软引用的对象的SoftReference对象清除掉。常用的方式为:

```java
SoftReference ref = null; 
while ((ref = (EmployeeRef) q.poll()) != null) { 
    // 清除ref 
} 
```

理解了ReferenceQueue的工作机制之后，我们就可以开始构造一个Java对象的高速缓存器了。

利用Java2平台垃圾收集机制的特性以及前述的垃圾对象重获方法，我们通过一个雇员信息查询系统的小例子来说明如何构建一种高速缓存器来避免重复构建同一个对象带来的性能损失。我们将一个雇员的档案信息定义为一个Employee类:
```java
public class Employee { 
    private String id;// 雇员的标识号码 
    private String name;// 雇员姓名 
    private String department;// 该雇员所在部门 
    private String Phone;// 该雇员联系电话 
    private int salary;// 该雇员薪资 
    private String origin;// 该雇员信息的来源 

    // 构造方法 
    public Employee(String id) { 
       this.id = id; 
       getDataFromlnfoCenter(); 
    } 

    // 到数据库中取得雇员信息 
    private void getDataFromlnfoCenter() { 
       // 和数据库建立连接井查询该雇员的信息，将查询结果赋值 
       // 给name，department，plone，salary等变量 
       // 同时将origin赋值为"From DataBase" 
    }
```
这个Employee类的构造方法中我们可以预见，如果每次需要查询一个雇员的信息。哪怕是几秒中之前刚刚查询过的，都要重新构建一个实例，这是需要消耗很多时间的。下面是一个对Employee对象进行缓存的缓存器的定义:
```java
import java.lang.ref.ReferenceQueue; 
import java.lang.ref.SoftReference; 
import java.util.Hashtable; 
public class EmployeeCache { 
    static private EmployeeCache cache;// 一个Cache实例 
    private Hashtable<String,EmployeeRef> employeeRefs;// 用于Chche内容的存储 
    private ReferenceQueue<Employee> q;// 垃圾Reference的队列 

    // 继承SoftReference，使得每一个实例都具有可识别的标识。 
    // 并且该标识与其在HashMap内的key相同。 
    private class EmployeeRef extends SoftReference<Employee> { 
       private String _key = ""; 

       public EmployeeRef(Employee em, ReferenceQueue<Employee> q) { 
           super(em, q); 
           _key = em.getID(); 
       } 
    } 

    // 构建一个缓存器实例 
    private EmployeeCache() { 
       employeeRefs = new Hashtable<String,EmployeeRef>(); 
       q = new ReferenceQueue<Employee>(); 
    } 

    // 取得缓存器实例 
    public static EmployeeCache getInstance() { 
       if (cache == null) { 
           cache = new EmployeeCache(); 
       } 
       return cache; 
    } 

    // 以软引用的方式对一个Employee对象的实例进行引用并保存该引用 
    private void cacheEmployee(Employee em) { 
       cleanCache();// 清除垃圾引用 
       EmployeeRef ref = new EmployeeRef(em, q); 
       employeeRefs.put(em.getID(), ref); 
    } 

    // 依据所指定的ID号，重新获取相应Employee对象的实例 
    public Employee getEmployee(String ID) { 
       Employee em = null; 
       // 缓存中是否有该Employee实例的软引用，如果有，从软引用中取得。 
       if (employeeRefs.containsKey(ID)) { 
           EmployeeRef ref = (EmployeeRef) employeeRefs.get(ID); 
           em = (Employee) ref.get(); 
       } 
       // 如果没有软引用，或者从软引用中得到的实例是null，重新构建一个实例， 
       // 并保存对这个新建实例的软引用 
       if (em == null) { 
           em = new Employee(ID); 
           System.out.println("Retrieve From EmployeeInfoCenter. ID=" + ID); 
           this.cacheEmployee(em); 
       } 
       return em; 
    } 

    // 清除那些所软引用的Employee对象已经被回收的EmployeeRef对象 
    private void cleanCache() { 
       EmployeeRef ref = null; 
       while ((ref = (EmployeeRef) q.poll()) != null) { 
           employeeRefs.remove(ref._key); 
       } 
    } 
 ```
